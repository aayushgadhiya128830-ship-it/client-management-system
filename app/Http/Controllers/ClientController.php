<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    private const PER_PAGE = 10;

    /** List clients (search + pagination, 10 per page). */
    public function index(Request $request): View
    {
        // ?search[]=x would be an array: only plain text is accepted
        $search = $request->query('search');
        $search = is_string($search) ? mb_substr(trim($search), 0, 100) : '';

        $clients = Client::search($search)
            ->latest()
            ->latest('id')              // stable order when created_at is equal
            ->paginate(self::PER_PAGE)
            ->withQueryString();        // keeps ?search=... on the page links

        return view('clients.index', compact('clients', 'search'));
    }

    /** Show the add-client form. */
    public function create(): View
    {
        return view('clients.create');
    }

    /** Validate and store a new client. */
    public function store(ClientRequest $request): RedirectResponse
    {
        try {
            Client::create($request->validated());
        } catch (UniqueConstraintViolationException) {
            // two users saved the same e-mail at the same moment
            return back()->withInput()->withErrors([
                'email' => 'A client with this e-mail address already exists.',
            ]);
        }

        return redirect()->route('clients.index')
            ->with('success', 'Client added successfully.');
    }

    /** Show the details of one client (404 if it does not exist). */
    public function show(Client $client): View
    {
        return view('clients.show', compact('client'));
    }

    /** Show the edit form. */
    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    /** Validate and update a client. */
    public function update(ClientRequest $request, Client $client): RedirectResponse
    {
        try {
            $client->update($request->validated());
        } catch (UniqueConstraintViolationException) {
            return back()->withInput()->withErrors([
                'email' => 'A client with this e-mail address already exists.',
            ]);
        }

        return redirect()->route('clients.index')
            ->with('success', 'Client updated successfully.');
    }

    /** Delete a client (DELETE method only). */
    public function destroy(Client $client): RedirectResponse
    {
        try {
            $client->delete();
        } catch (QueryException $e) {
            report($e); // logged for the developer, not shown to the user

            return redirect()->route('clients.index')
                ->with('error', 'The client could not be deleted. Please try again.');
        }

        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
