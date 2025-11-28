<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderFile extends Model
{
    use HasFactory;

    /**
     * File type constants
     */
    const TYPE_SOURCE_CODE = 'source_code';
    const TYPE_DATABASE = 'database';
    const TYPE_DOCUMENTATION = 'documentation';
    const TYPE_DESIGN = 'design';
    const TYPE_OTHER = 'other';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'description',
        'uploaded_by',
        'version',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship with user who uploaded the file
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get display file size
     */
    public function getDisplayFileSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get file icon based on type
     */
    public function getFileIconAttribute(): string
    {
        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);

        $icons = [
            'zip' => 'file-archive',
            'rar' => 'file-archive',
            'pdf' => 'file-text',
            'doc' => 'file-text',
            'docx' => 'file-text',
            'xls' => 'file-spreadsheet',
            'xlsx' => 'file-spreadsheet',
            'sql' => 'database',
            'jpg' => 'image',
            'jpeg' => 'image',
            'png' => 'image',
            'psd' => 'image',
            'ai' => 'image',
        ];

        return $icons[$extension] ?? 'file';
    }

    /**
     * Check if file is downloadable
     */
    public function isDownloadable(): bool
    {
        return file_exists(storage_path('app/' . $this->file_path));
    }

    /**
     * Scope for source code files
     */
    public function scopeSourceCode($query)
    {
        return $query->where('file_type', self::TYPE_SOURCE_CODE);
    }

    /**
     * Scope for documentation files
     */
    public function scopeDocumentation($query)
    {
        return $query->where('file_type', self::TYPE_DOCUMENTATION);
    }
}
