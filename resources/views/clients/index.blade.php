@extends('layouts.app')

@section('title', 'Clients')

@section('content')
    <div class="hd">
        <div class="l">
            <span class="neu-icon hd-icon"><x-icon name="users" :size="24" /></span>
            <div>
                <h2>Client List</h2>
                <p>Manage your clients with ease.</p>
            </div>
        </div>
        <a href="{{ route('clients.create') }}" class="neu-btn"><x-icon name="plus" :size="16" /> Add New Client</a>
    </div>

    {{-- Search --}}
    <form action="{{ route('clients.index') }}" method="GET" class="search-row" role="search">
        <div class="search-wrap">
            <x-icon name="search" :size="18" />
            <input type="search" name="search" value="{{ $search }}" class="neu-input"
                   placeholder="Search by name, email or phone…" aria-label="Search clients">
        </div>
        <button type="submit" class="neu-btn">Search</button>
        <a href="{{ route('clients.index') }}" class="neu-btn neu-btn-muted clear-btn" id="clear-search"
           @if ($search === '') hidden @endif>Clear</a>
    </form>

    {{-- Everything inside #results is replaced by the live search (JavaScript) --}}
    <div id="results" aria-live="polite">
    <div class="table-wrap">
        <table class="clients-table">
            <caption class="visually-hidden">List of clients</caption>
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">Company</th>
                <th scope="col">City</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($clients as $client)
                <tr>
                    <td data-label="#">{{ $clients->firstItem() + $loop->index }}</td>
                    <td data-label="Name">
                        <span class="av">{{ $client->initials }}</span>
                        <span class="nm">{{ $client->name }}</span>
                    </td>
                    <td data-label="Email">{{ $client->email }}</td>
                    <td data-label="Phone">{{ $client->phone }}</td>
                    <td data-label="Company">{{ $client->company_name ?: '—' }}</td>
                    <td data-label="City">{{ $client->city ?: '—' }}</td>
                    <td data-label="Status">
                        <span class="neu-badge {{ $client->isActive() ? 'ok' : 'bad' }}">{{ ucfirst($client->status) }}</span>
                    </td>
                    <td data-label="Actions">
                        <div class="ab">
                            <a href="{{ route('clients.show', $client) }}" class="neu-icon" title="View" aria-label="View {{ $client->name }}">
                                <x-icon name="eye" :size="15" />
                            </a>
                            <a href="{{ route('clients.edit', $client) }}" class="neu-icon" title="Edit" aria-label="Edit {{ $client->name }}">
                                <x-icon name="edit" :size="15" />
                            </a>
                            <button type="button" class="neu-icon danger" title="Delete" aria-label="Delete {{ $client->name }}"
                                    data-dialog="del{{ $client->id }}">
                                <x-icon name="trash" :size="15" />
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty">
                        No clients found.
                        <a href="{{ route('clients.create') }}">Add the first client</a>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="pg">
        <div>Showing {{ $clients->firstItem() ?? 0 }} to {{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }} clients</div>
        {{ $clients->links() }}
    </div>

    {{-- One delete dialog per row on this page --}}
    @foreach ($clients as $client)
        @include('clients._delete_modal', ['client' => $client])
    @endforeach
    </div>{{-- /#results --}}
@endsection
