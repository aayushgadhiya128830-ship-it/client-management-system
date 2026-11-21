{{-- Delete confirmation: native <dialog>, opened with data-dialog="del{id}" --}}
<dialog id="del{{ $client->id }}" class="neu-dialog" aria-labelledby="delLabel{{ $client->id }}">
    <div class="modal-box">
        <span class="neu-icon warn-icon"><x-icon name="warn" :size="30" /></span>
        <h3 id="delLabel{{ $client->id }}">Delete Client?</h3>
        <p>Are you sure you want to delete <b>{{ $client->name }}</b>?<br>This action cannot be undone.</p>

        <form action="{{ route('clients.destroy', $client) }}" method="POST" class="acts acts-center">
            @csrf
            @method('DELETE')
            <button type="button" class="neu-btn neu-btn-muted" data-dialog-close>Cancel</button>
            <button type="submit" class="neu-btn danger"><x-icon name="trash" :size="16" /> Yes, Delete</button>
        </form>
    </div>
</dialog>
