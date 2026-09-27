@extends('layouts.app')
@section('title', 'Kelola Pengguna')
@section('topbar-title', 'Kelola Pengguna')
@section('topbar-subtitle', 'Kelola hak akses dan akun pengguna sistem')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
    <circle cx="9" cy="7" r="4"/>
    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
  </svg>
@endsection

@section('content')
<div class="content-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 28px;">
  <div>
    <h2 class="content-title" style="font-size: 28px; font-weight: 800; letter-spacing: -0.5px; color: var(--text-primary);">Daftar Akun Pengguna</h2>
    <p class="content-subtitle" style="font-size: 14px; color: var(--text-secondary); margin-top: 6px;">Kelola hak akses (role) pengguna yang telah login via Google. Atur dengan cermat.</p>
  </div>
  <button type="button" class="btn btn-primary" onclick="document.getElementById('addUserModal').style.display='flex'" style="padding: 10px 20px; font-size: 14px; border-radius: 8px; box-shadow: 0 4px 12px rgba(16,185,129,0.3);">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
    Tambah Pengguna Baru
  </button>
</div>

<div class="card" style="margin-bottom: 24px; padding: 16px 24px; border-radius: 12px;">
  <form method="GET" action="{{ route('users.index') }}" style="display: flex; gap: 12px; align-items: center;">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau role pengguna..." class="form-control" style="flex: 1; padding: 10px 14px; border-radius: 8px;">
    <button type="submit" class="btn btn-primary" style="padding: 10px 20px; border-radius: 8px;">Cari</button>
    @if(request()->filled('search'))
      <a href="{{ route('users.index') }}" style="color: var(--text-secondary); text-decoration: none; font-size: 13px; font-weight: 600; padding: 10px 14px;">Reset</a>
    @endif
  </form>
</div>

@if($users->isEmpty())
<div class="card" style="padding: 60px 40px; text-align: center; border-radius: 16px; border: 1px dashed var(--border);">
  <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
  </div>
  <h3 style="font-size: 18px; color: var(--text-primary); margin-bottom: 8px;">Belum Ada Pengguna</h3>
  <p style="color: var(--text-muted); font-size: 14px; margin: 0; max-width: 400px; margin-inline: auto; line-height: 1.5;">
    Pengguna akan otomatis muncul di sini setelah mereka melakukan login pertama kali menggunakan akun Google.
  </p>
</div>
@else
<div class="card" style="border-radius: 16px; overflow: hidden; padding: 0;">
  <div class="table-responsive" style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
    <table class="table" style="min-width: 800px; border-collapse: separate; border-spacing: 0;">
      <thead>
        <tr style="background: var(--bg-surface);">
          <th style="width: 60px; padding: 16px 24px; border-bottom: 1px solid var(--border);">NO</th>
          <th style="padding: 16px 24px; border-bottom: 1px solid var(--border);">NAMA PENGGUNA</th>
          <th style="padding: 16px 24px; border-bottom: 1px solid var(--border);">EMAIL GOOGLE</th>
          <th style="padding: 16px 24px; border-bottom: 1px solid var(--border);">STATUS</th>
          <th style="padding: 16px 24px; border-bottom: 1px solid var(--border); width: 220px;">HAK AKSES</th>
          <th style="width: 140px; text-align: right; padding: 16px 24px; border-bottom: 1px solid var(--border);">AKSI</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $index => $u)
        <tr style="transition: all 0.2s ease;">
          <td style="padding: 20px 24px; font-weight: 600; color: var(--text-secondary);">{{ $index + 1 }}</td>
          <td style="padding: 20px 24px;">
            <div style="display:flex; align-items:center; gap:14px">
              <div style="width:40px; height:40px; border-radius:50%; background:var(--accent); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:15px; box-shadow: 0 2px 6px rgba(16,185,129,0.3);">
                {{ strtoupper(substr($u->name, 0, 1)) }}
              </div>
              <div>
                <strong style="font-size: 14.5px; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                  {{ $u->name }}
                  @if($u->id === auth()->id())
                    <span style="font-size:10px; background:rgba(16,185,129,0.15); color:var(--accent); padding:3px 8px; border-radius:12px; font-weight:700; text-transform: uppercase; letter-spacing: 0.5px;">Anda</span>
                  @endif
                </strong>
              </div>
            </div>
          </td>
          <td style="padding: 20px 24px; font-size: 13.5px; color: var(--text-secondary);">{{ $u->email }}</td>
          <td style="padding: 20px 24px;">
            <div style="display: inline-flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 600; padding: 6px 12px; border-radius: 20px; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #34d399;">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              Aktif
            </div>
          </td>
          <td style="padding: 20px 24px;">
            <form action="{{ route('users.update', $u) }}" method="POST" id="roleForm{{ $u->id }}" style="margin: 0;">
              @csrf
              @method('PATCH')
              <select name="role" class="form-control" style="font-size:13.5px; padding: 8px 12px; border-radius: 8px; cursor: pointer; border-color: var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.05);" onchange="markChanged({{ $u->id }})">
                @foreach($roles as $role)
                  <option value="{{ $role }}" {{ $u->role === $role ? 'selected' : '' }}>{{ $role }}</option>
                @endforeach
              </select>
            </form>
          </td>
          <td style="padding: 20px 24px; text-align: right;">
            <div style="display:flex; justify-content:flex-end; gap:8px;">
              <button
                type="button"
                id="saveBtn{{ $u->id }}"
                onclick="document.getElementById('roleForm{{ $u->id }}').submit()"
                class="btn btn-primary"
                style="font-size:13px; padding:8px 16px; border-radius: 8px; opacity:0; pointer-events: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); transform: translateX(10px);">
                Simpan
              </button>
              
              <button type="button"
                      data-id="{{ $u->id }}"
                      data-name="{{ $u->name }}"
                      data-email="{{ $u->email }}"
                      data-role="{{ $u->role }}"
                      onclick="openEditUserModal(this)"
                      style="background:rgba(59,130,246,0.1); color:#3b82f6; padding:8px; min-width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; border:1px solid rgba(59,130,246,0.2); transition:all 0.2s; cursor:pointer;"
                      title="Edit Pengguna">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
              </button>

              @if(auth()->id() !== $u->id)
                <button type="button"
                        data-url="{{ route('users.destroy', $u) }}"
                        data-title="Hapus Pengguna?"
                        data-name="{{ $u->name }} ({{ $u->email }})"
                        data-type="pengguna"
                        onclick="confirmDelete(this)"
                        style="background:rgba(239,68,68,0.1); color:#f87171; padding:8px; min-width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; border:1px solid rgba(239,68,68,0.2); transition:all 0.2s; cursor:pointer;"
                        title="Hapus Pengguna"
                        onmouseover="this.style.background='rgba(239,68,68,0.2)'"
                        onmouseout="this.style.background='rgba(239,68,68,0.1)'">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                  </svg>
                </button>
                <form id="deleteForm{{ $u->id }}" action="{{ route('users.destroy', $u) }}" method="POST" style="display:none;">
                  @csrf
                  @method('DELETE')
                </form>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

<script>
  function markChanged(userId) {
    const btn = document.getElementById('saveBtn' + userId);
    btn.disabled = false;
    btn.style.opacity = '1';
    btn.style.pointerEvents = 'auto';
    btn.style.transform = 'translateX(0)';
  }

  function closeAddUserModal() {
    document.getElementById('addUserModal').style.display = 'none';
  }

  function openEditUserModal(btn) {
    const id = btn.dataset.id;
    const name = btn.dataset.name || '';
    const email = btn.dataset.email || '';
    const role = btn.dataset.role || '';
    document.getElementById('editUserForm').action = '/users/' + id;
    document.getElementById('editNameInput').value = name;
    document.getElementById('editEmailInput').value = email;
    document.getElementById('editRoleInput').value = role;
    document.getElementById('editUserModal').style.display = 'flex';
  }

  function closeEditUserModal() {
    document.getElementById('editUserModal').style.display = 'none';
  }
</script>

{{-- Tambah Pengguna Modal --}}
<div class="modal-backdrop" id="addUserModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
  <div class="card" style="width: 100%; max-width: 480px; padding: 32px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); border: 1px solid var(--border);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
      <h3 style="margin:0; font-size:20px; font-weight:800; color: var(--text-primary);">Tambah Pengguna Baru</h3>
      <button type="button" onclick="closeAddUserModal()" style="background:var(--bg-base); border:1px solid var(--border); border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; color:var(--text-muted); cursor:pointer; transition: 0.2s;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    
    <div style="background:rgba(59,130,246,0.1); border:1px solid rgba(59,130,246,0.2); padding:16px; border-radius:12px; margin-bottom:24px; font-size:13px; color:var(--text-secondary); line-height:1.6;">
      <div style="display: flex; gap: 8px; margin-bottom: 6px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        <strong style="color: #3b82f6;">Informasi Penting</strong>
      </div>
      Tambahkan email Google dan Role di sini. Pengguna tetap <strong>harus ditambahkan ke Google Cloud Test Users</strong> secara manual agar bisa login.
    </div>

    <form action="{{ route('users.store') }}" method="POST">
      @csrf
      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label" style="font-weight: 600; margin-bottom: 6px; display: block;">Nama Lengkap</label>
        <input type="text" name="name" class="form-control" required placeholder="Contoh: Budi Santoso" style="padding: 12px 14px; border-radius: 8px;">
      </div>
      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label" style="font-weight: 600; margin-bottom: 6px; display: block;">Email Google</label>
        <input type="email" name="email" class="form-control" required placeholder="Contoh: budi@gmail.com" style="padding: 12px 14px; border-radius: 8px;">
      </div>
      <div class="form-group" style="margin-bottom: 24px;">
        <label class="form-label" style="font-weight: 600; margin-bottom: 6px; display: block;">Hak Akses (Role)</label>
        <select name="role" class="form-control" required style="padding: 12px 14px; border-radius: 8px;">
          @foreach($roles as $role)
            <option value="{{ $role }}">{{ $role }}</option>
          @endforeach
        </select>
      </div>
      <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:32px;">
        <button type="button" class="btn btn-ghost" onclick="closeAddUserModal()" style="padding: 10px 20px; border-radius: 8px;">Batal</button>
        <button type="submit" class="btn btn-primary" style="padding: 10px 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(16,185,129,0.3);">Simpan Pengguna</button>
      </div>
    </form>
  </div>
</div>

{{-- Edit Pengguna Modal --}}
<div class="modal-backdrop" id="editUserModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
  <div class="card" style="width: 100%; max-width: 480px; padding: 32px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); border: 1px solid var(--border);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
      <h3 style="margin:0; font-size:20px; font-weight:800; color: var(--text-primary);">Edit Data Pengguna</h3>
      <button type="button" onclick="closeEditUserModal()" style="background:var(--bg-base); border:1px solid var(--border); border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; color:var(--text-muted); cursor:pointer; transition: 0.2s;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <form id="editUserForm" action="" method="POST">
      @csrf
      @method('PATCH')
      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label" style="font-weight: 600; margin-bottom: 6px; display: block;">Nama Lengkap</label>
        <input type="text" name="name" id="editNameInput" class="form-control" required style="padding: 12px 14px; border-radius: 8px;">
      </div>
      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label" style="font-weight: 600; margin-bottom: 6px; display: block;">Email Google</label>
        <input type="email" name="email" id="editEmailInput" class="form-control" required style="padding: 12px 14px; border-radius: 8px;">
      </div>
      <div class="form-group" style="margin-bottom: 24px;">
        <label class="form-label" style="font-weight: 600; margin-bottom: 6px; display: block;">Hak Akses (Role)</label>
        <select name="role" id="editRoleInput" class="form-control" required style="padding: 12px 14px; border-radius: 8px;">
          @foreach($roles as $role)
            <option value="{{ $role }}">{{ $role }}</option>
          @endforeach
        </select>
      </div>
      <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:32px;">
        <button type="button" class="btn btn-ghost" onclick="closeEditUserModal()" style="padding: 10px 20px; border-radius: 8px;">Batal</button>
        <button type="submit" class="btn btn-primary" style="padding: 10px 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(16,185,129,0.3);">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
@endsection
