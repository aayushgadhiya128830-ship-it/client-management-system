@extends('layouts.app')

@section('title', $client->name)

@section('content')
    <div class="hd">
        <div class="l">
            <span class="neu-icon hd-icon"><x-icon name="eye" :size="24" /></span>
            <div>
                <h2>Client Details</h2>
                <p>Complete information of the selected client.</p>
            </div>
        </div>
    </div>

    <div class="det">
        <div class="bigav">{{ $client->initials }}</div>
        <div>
            <h3>{{ $client->name }}</h3>
            <p>{{ $client->company_name ?: '—' }}</p>
            <span class="neu-badge {{ $client->isActive() ? 'ok' : 'bad' }}">{{ ucfirst($client->status) }}</span>
        </div>
    </div>

    <div class="info-grid">
        <div class="tile neu-inset"><small>Email</small><span>{{ $client->email }}</span></div>
        <div class="tile neu-inset"><small>Phone</small><span>{{ $client->phone }}</span></div>
        <div class="tile neu-inset"><small>City</small><span>{{ $client->city ?: '—' }}</span></div>
        <div class="tile neu-inset"><small>Company</small><span>{{ $client->company_name ?: '—' }}</span></div>
        <div class="tile neu-inset full"><small>Address</small><span>{{ $client->address ?: '—' }}</span></div>
        <div class="tile neu-inset"><small>Created</small><span>{{ $client->created_at->format('d M Y, h:i A') }}</span></div>
        <div class="tile neu-inset"><small>Last updated</small><span>{{ $client->updated_at->format('d M Y, h:i A') }}</span></div>
    </div>

    <div class="acts">
        <a href="{{ route('clients.edit', $client) }}" class="neu-btn"><x-icon name="edit" :size="16" /> Edit Client</a>
        <button type="button" class="neu-btn danger" data-dialog="del{{ $client->id }}">
            <x-icon name="trash" :size="16" /> Delete
        </button>
        <a href="{{ route('clients.index') }}" class="neu-btn neu-btn-muted"><x-icon name="back" :size="16" /> Back to List</a>
    </div>

    @include('clients._delete_modal', ['client' => $client])
@endsection
