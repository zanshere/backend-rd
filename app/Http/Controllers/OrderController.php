<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Package;
use App\Models\OrderProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Show the order creation form
     */
    public function create()
    {
        $packages = Package::where('is_active', true)
            ->orderBy('base_price')
            ->get();

        return view('order-landing', compact('packages'));
    }

    /**
     * Store a new order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'project_name' => 'required|string|max:255',
            'domain_name' => 'required|string|max:100',
            'special_requirements' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $package = Package::findOrFail($validated['package_id']);

            // Generate order number
            $orderNumber = 'ORD' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Calculate prices
            $basePrice = $package->is_custom_price ? 0 : $package->base_price;
            $discountAmount = 0; // Add discount logic here if needed
            $totalPrice = $basePrice - $discountAmount;

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'package_id' => $package->id,
                'project_name' => $validated['project_name'],
                'domain_name' => $validated['domain_name'],
                'special_requirements' => $validated['special_requirements'] ?
                    ['kebutuhan_khusus' => $validated['special_requirements']] : null,
                'base_price' => $basePrice,
                'discount_amount' => $discountAmount,
                'total_price' => $totalPrice,
                'status' => $package->is_custom_price ? Order::STATUS_CONFIRMED : Order::STATUS_PENDING,
                'payment_status' => $package->is_custom_price ? Order::PAYMENT_PAID : Order::PAYMENT_PENDING,
                'admin_notes' => 'Order created by user'
            ]);

            DB::commit();

            // Return response based on package type
            if ($package->is_custom_price) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order berhasil dibuat! Tim kami akan menghubungi Anda untuk konsultasi gratis.',
                    'order_id' => $order->id,
                    'redirect' => route('orders.show', $order->id)
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Order berhasil dibuat! Silakan lanjutkan pembayaran.',
                    'order_id' => $order->id,
                    'redirect' => route('payment.page', $order->id)
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat order. Silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save order as draft
     */
    public function saveDraft(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'project_name' => 'nullable|string|max:255',
            'domain_name' => 'nullable|string|max:100',
            'special_requirements' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $package = Package::findOrFail($validated['package_id']);

            // Generate order number
            $orderNumber = 'ORD' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'package_id' => $package->id,
                'project_name' => $validated['project_name'],
                'domain_name' => $validated['domain_name'],
                'special_requirements' => $validated['special_requirements'] ?
                    ['kebutuhan_khusus' => $validated['special_requirements']] : null,
                'base_price' => $package->is_custom_price ? 0 : $package->base_price,
                'discount_amount' => 0,
                'total_price' => $package->is_custom_price ? 0 : $package->base_price,
                'status' => Order::STATUS_DRAFT,
                'payment_status' => Order::PAYMENT_PENDING,
                'admin_notes' => 'Draft order created'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil disimpan sebagai draft.',
                'order_id' => $order->id,
                'redirect' => route('orders.show', $order->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Save draft failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan draft. Silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Order::with(['package', 'latestProgress'])
            ->where('user_id', $user->id)
            ->latest();

        if ($status && in_array($status, ['draft', 'pending', 'confirmed', 'progress', 'completed', 'cancelled'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%")
                  ->orWhere('domain_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(10);

        return view('orders.index', compact('orders', 'status', 'search'));
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        $order = Order::with(['package', 'progressUpdates' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->where('user_id', Auth::id())->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    /**
     * Update order status (for admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,pending,confirmed,progress,completed,cancelled',
            'notes' => 'nullable|string|max:500'
        ]);

        $order = Order::findOrFail($id);

        // Save old status for log
        $oldStatus = $order->status;
        $newStatus = $request->status;

        $order->update([
            'status' => $newStatus,
            'admin_notes' => $request->notes ?: $order->admin_notes
        ]);

        // Log status change
        Log::info('Order status updated', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'updated_by' => Auth::id(),
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status order berhasil diperbarui',
            'order' => $order->fresh()
        ]);
    }

    /**
     * Add progress update to order
     */
    public function addProgress(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'percentage' => 'required|integer|min:0|max:100',
            'status' => 'required|in:not_started,in_progress,completed'
        ]);

        $order = Order::findOrFail($id);

        $progress = OrderProgress::create([
            'order_id' => $order->id,
            'title' => $request->title,
            'description' => $request->description,
            'percentage' => $request->percentage,
            'status' => $request->status,
            'started_at' => $request->status === 'in_progress' ? now() : null,
            'completed_at' => $request->status === 'completed' ? now() : null
        ]);

        // If progress reaches 100%, update order status to completed
        if ($request->percentage === 100) {
            $order->update(['status' => Order::STATUS_COMPLETED]);
        } elseif ($order->status !== Order::STATUS_IN_PROGRESS && $request->percentage > 0) {
            $order->update(['status' => Order::STATUS_IN_PROGRESS]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Progress berhasil ditambahkan',
            'progress' => $progress,
            'order' => $order->fresh()
        ]);
    }

    /**
     * Get order details for API
     */
    public function getOrderDetails($id)
    {
        $order = Order::with(['package', 'progressUpdates' => function($query) {
            $query->orderBy('created_at', 'desc');
        }, 'user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'order' => $order,
            'progress_updates' => $order->progressUpdates,
            'overall_progress' => $order->overall_progress
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        // Only orders with certain status can be cancelled
        if (!in_array($order->status, [Order::STATUS_DRAFT, Order::STATUS_PENDING])) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak dapat dibatalkan karena sudah diproses'
            ], 400);
        }

        $order->update([
            'status' => Order::STATUS_CANCELLED,
            'payment_status' => Order::PAYMENT_FAILED,
            'admin_notes' => 'Order dibatalkan oleh user pada ' . now()->format('Y-m-d H:i:s')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dibatalkan'
        ]);
    }
}
