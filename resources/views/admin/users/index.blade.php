@extends('admin.layout')
@section('title','المستخدمون')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
  <h3>قائمة المستخدمين</h3>
  <div></div>
</div>

<div style="overflow:auto">
  <table style="width:100%;border-collapse:collapse">
    <thead>
      <tr>
        <th>#</th><th>الاسم</th><th>البريد</th><th>الدور</th><th>نشط</th><th>إجراءات</th>
      </tr>
    </thead>
    <tbody>
    @foreach($users as $u)
      <tr style="border-bottom:1px solid var(--border)">
        <td>{{ $u->id }}</td>
        <td>{{ $u->name }}</td>
        <td>{{ $u->email }}</td>
        <td>{{ $u->role }}</td>
        <td>{{ $u->active ? 'نعم' : 'لا' }}</td>
        <td>
          <form method="POST" action="{{ route('admin.users.toggle', $u) }}" style="display:inline">@csrf<button class="btn">{{ $u->active ? 'تعطيل' : 'تفعيل' }}</button></form>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>

<div style="margin-top:12px">{{ $users->links() }}</div>

@endsection
