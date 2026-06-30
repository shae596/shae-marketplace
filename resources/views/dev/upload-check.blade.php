@extends('layouts.app')

@section('content')
<div class="card p-4">
    <h1 class="h4">Diagnostic upload (local)</h1>
    <p class="text-muted">Page de test — supprimee en production.</p>

    <table class="table table-sm mt-3">
        @foreach($checks as $label => $value)
            <tr>
                <th style="width:40%">{{ $label }}</th>
                <td @class(['text-danger fw-bold' => str_starts_with((string) $value, 'NON') || str_starts_with((string) $value, 'ECHEC')])>{{ $value }}</td>
            </tr>
        @endforeach
    </table>

    <hr>
    <h2 class="h5">Test upload rapide</h2>
    <form method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-end">
        @csrf
        <div>
            <input type="file" name="test_image" class="form-control" accept="image/*" required>
        </div>
        <button class="btn btn-shae">Tester</button>
    </form>

    <p class="mt-4 small text-muted mb-0">
        Si <strong>upload_tmp_dir</strong> est vide ici, relancez avec <code>lancer-shae.bat</code> ou <code>.\lancer-shae.ps1</code>.
        Utilisez toujours <strong>http://127.0.0.1:8000</strong> (pas localhost sans port).
    </p>
</div>
@endsection
