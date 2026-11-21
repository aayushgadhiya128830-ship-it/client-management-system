@extends('layouts.app')

@section('title', 'Edit Client')

@section('content')
    <div class="hd">
        <div class="l">
            <span class="neu-icon hd-icon"><x-icon name="edit" :size="24" /></span>
            <div>
                <h2>Edit Client</h2>
                <p>Update the information of this client.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('clients.update', $client) }}" method="POST" novalidate>
        @csrf
        @method('PUT')
        @include('clients._form', ['submit' => 'Update Client'])
    </form>
@endsection
