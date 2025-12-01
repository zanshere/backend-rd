<!-- resources/views/payment/show.blade.php -->
@extends('layouts.app')

@section('content')
<livewire:payment :order="$order->id" />
@endsection
