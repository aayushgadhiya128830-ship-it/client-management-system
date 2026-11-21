@extends('layouts.app')

@section('title', 'Add Client')

@section('content')
    <div class="hd">
        <div class="l">
            <span class="neu-icon hd-icon"><x-icon name="plus" :size="24" /></span>
            <div>
                <h2>Add New Client</h2>
                <p>Enter the details of the new client.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('clients.store') }}" method="POST" novalidate>
        @csrf
        @include('clients._form', ['submit' => 'Save Client'])
    </form>
@endsection
