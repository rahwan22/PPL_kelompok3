@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="h3 font-weight-bold text-primary text-center m-0">
            <i class="fas fa-chalkboard-teacher me-2"></i> Daftar Guru
        </h2>
    
    @if (auth()->user()->role !== 'kepala_sekolah')
        <a href="{{ route('guru.create') }}" class="btn btn-success mb-3">+ Tambah Guru</a>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Nama</th>
                <th>NIP</th>
                <th>Mapel</th>
                
                
                 @if (auth()->user()->role === 'admin')
                <th><center>Aksi</center></th>
                 @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $g)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $g->nama }}</td>
                <td>{{ $g->nip }}</td>
                <td>{{ $g->mapel }}</td>
                

                 @if (auth()->user()->role === 'admin')
                <td class="text-center">
                    <a href="{{ route('guru.show', $g->id_guru) }}"  class="btn btn-info btn-sm me-2" title="Detail siswa">
                            <i class="fas fa-eye"></i>
                        </a>
                    <a href="{{ route('guru.edit', $g->id_guru) }}" class="btn btn-primary btn-sm me-2" title="Edit Data">
                            <i class="fas fa-edit"></i>
                        </a>

                <form id="delete-form-{{ $g->id_guru }}" action="{{ route('guru.destroy', $g->id_guru) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="delete_user" id="delete_user_{{ $g->id_guru }}" value="0">
                    <button type="button" class="btn btn-danger btn-sm"
                        onclick="confirmDelete('{{ $g->nama }}', '{{ $g->id_guru }}')">
                        Hapus
                    </button>
                </form>

                </td>
                 @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $data->links() }}
</div>
@endsection
