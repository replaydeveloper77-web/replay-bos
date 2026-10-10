<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['dashboard', 'dashboard.view', 'Lihat Dashboard'],

            ['ticket', 'ticket.view', 'Lihat Ticket'],
            ['ticket', 'ticket.create', 'Buat Ticket'],
            ['ticket', 'ticket.update', 'Update Ticket'],
            ['ticket', 'ticket.assign', 'Assign Ticket'],
            ['ticket', 'ticket.escalate', 'Escalate Ticket'],
            ['ticket', 'ticket.close', 'Close Ticket'],

            ['device', 'device.view', 'Lihat Device'],
            ['device', 'device.create', 'Tambah Device'],
            ['device', 'device.update', 'Update Device'],

            ['change', 'change.create', 'Buat Change Request'],
            ['change', 'change.approve', 'Approve Change Request'],
            ['change', 'change.execute', 'Eksekusi Change Request'],
            ['change', 'change.rollback', 'Rollback Change Request'],

            ['audit', 'audit.view', 'Lihat Audit Trail'],

            ['user', 'user.view', 'Lihat User'],
            ['user', 'user.create', 'Tambah User'],
            ['user', 'user.update', 'Update User'],
            ['user', 'user.disable', 'Nonaktifkan User'],

            ['role', 'role.view', 'Lihat Role'],
            ['role', 'role.manage', 'Kelola Permission Role'],

            ['customer', 'customer.view', 'Lihat Pelanggan'],
            ['customer', 'customer.create', 'Tambah Data Pelanggan/Prospek'],
            ['customer', 'customer.manage', 'Kelola Penuh Data Pelanggan (edit/nonaktifkan)'],
            ['customer', 'customer.activate', 'Tandai Pelanggan Baru Selesai Diaktivasi'],

            ['team', 'team.view', 'Lihat Tim'],
            ['team', 'team.manage', 'Kelola Tim & Anggota'],

            ['whatsapp', 'whatsapp.send', 'Kirim WhatsApp'],
            ['whatsapp', 'whatsapp.template.manage', 'Kelola Template WhatsApp'],

            ['coverage', 'coverage.manage', 'Kelola Titik Coverage'],
            ['package', 'package.manage', 'Kelola Paket Layanan'],
            ['quotation', 'quotation.view', 'Lihat Surat Penawaran'],
            ['quotation', 'quotation.create', 'Buat Surat Penawaran'],
            ['quotation', 'quotation.manage', 'Kelola Semua Surat Penawaran'],
            ['invoice', 'invoice.view', 'Lihat Invoice / Closing'],
            ['invoice', 'invoice.create', 'Proses Closing & Buat Invoice'],
            ['invoice', 'invoice.manage', 'Kelola Pembayaran Invoice & Generate Tagihan Bulanan'],
            ['settings', 'settings.manage', 'Kelola Pengaturan Perusahaan'],
            ['api', 'api.manage', 'Kelola API Token (integrasi RADIUS/Payment Gateway)'],
            ['reseller', 'reseller.portal.view', 'Akses Portal Reseller (lihat pelanggan Home milik sendiri)'],

            ['employee', 'employee.view', 'Lihat Data Karyawan'],
            ['employee', 'employee.create', 'Tambah Data Karyawan'],
            ['employee', 'employee.manage', 'Kelola Penuh Data Karyawan (edit/resign)'],

            ['asset', 'asset.view', 'Lihat Aset / Inventaris'],
            ['asset', 'asset.manage', 'Kelola Penuh Aset (tambah, edit, ubah status)'],

            ['workorder', 'workorder.view', 'Lihat Work Order Instalasi'],
            ['workorder', 'workorder.manage', 'Kelola Penuh Work Order (buat, assign, ubah status)'],

            ['attendance', 'attendance.view', 'Lihat Laporan Absensi Semua Karyawan'],
            ['attendance', 'attendance.manage', 'Koreksi Data Absensi'],

            ['leave', 'leave.view', 'Lihat Semua Pengajuan Cuti/Izin'],
            ['leave', 'leave.manage', 'Setujui/Tolak Pengajuan Cuti/Izin'],

            ['fieldvisit', 'fieldvisit.view', 'Lihat Laporan Kunjungan Lapangan'],
            ['fieldvisit', 'fieldvisit.manage', 'Buat & Kelola Laporan Kunjungan Lapangan'],

            ['contract', 'contract.template.manage', 'Edit Redaksi Template PKWT/PKWTT'],
            ['teknisi', 'teknisi.portal.view', 'Akses Portal Teknisi (lihat dashboard tugas work order & tiket lapangan)'],
            ['package', 'package.change.view', 'Lihat Permohonan Upgrade/Downgrade Paket'],
            ['package', 'package.change.create', 'Ajukan Permohonan Upgrade/Downgrade Paket'],
            ['package', 'package.change.approve', 'Persetujuan (Approve/Reject) Upgrade/Downgrade Paket (Finance)'],
            ['database', 'database.monitor', 'Monitoring Resource Database & Server (cPanel)'],
        ];

        foreach ($permissions as [$module, $slug, $name]) {
            Permission::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'module' => $module],
            );
        }

        $roles = [
            [
                'slug' => 'noc-monitor',
                'name' => 'NOC Monitor',
                'description' => 'Memantau dashboard, membuat dan melihat ticket, tidak dapat melakukan perubahan kritis.',
                'permissions' => ['dashboard.view', 'ticket.view', 'ticket.create', 'ticket.update', 'device.view', 'customer.view', 'team.view', 'asset.view', 'workorder.view', 'fieldvisit.view'],
            ],
            [
                'slug' => 'noc-operator',
                'name' => 'NOC Operator',
                'description' => 'Menangani ticket, assign, eskalasi, dan komunikasi dengan pelanggan.',
                'permissions' => ['dashboard.view', 'ticket.view', 'ticket.create', 'ticket.update', 'ticket.assign', 'ticket.escalate', 'device.view', 'whatsapp.send', 'customer.view', 'customer.activate', 'team.view', 'asset.view', 'workorder.view', 'fieldvisit.view'],
            ],
            [
                'slug' => 'noc-engineer',
                'name' => 'NOC Engineer',
                'description' => 'Network troubleshooting, device management, dan pengajuan change request.',
                'permissions' => ['dashboard.view', 'ticket.view', 'ticket.create', 'ticket.update', 'ticket.assign', 'ticket.escalate', 'ticket.close', 'device.view', 'device.create', 'device.update', 'change.create', 'whatsapp.send', 'customer.view', 'customer.activate', 'team.view', 'asset.view', 'asset.manage', 'workorder.view', 'workorder.manage', 'fieldvisit.view', 'fieldvisit.manage'],
            ],
            [
                'slug' => 'noc-admin',
                'name' => 'NOC Admin',
                'description' => 'User management, role management, asset management, audit trail, dan konfigurasi notifikasi.',
                'permissions' => ['dashboard.view', 'user.view', 'user.create', 'user.update', 'user.disable', 'role.view', 'audit.view', 'device.view', 'device.create', 'device.update', 'whatsapp.template.manage', 'customer.view', 'customer.manage', 'customer.activate', 'team.view', 'team.manage', 'coverage.manage', 'package.manage', 'quotation.view', 'quotation.create', 'quotation.manage', 'invoice.view', 'invoice.create', 'invoice.manage', 'asset.view', 'asset.manage', 'workorder.view', 'workorder.manage', 'attendance.view', 'attendance.manage', 'leave.view', 'leave.manage', 'fieldvisit.view', 'fieldvisit.manage', 'contract.template.manage', 'package.change.view', 'package.change.create', 'database.monitor'],
            ],
            [
                'slug' => 'marketing',
                'name' => 'Marketing',
                'description' => 'Input data pelanggan (prospek maupun yang sudah aktif/berlangganan), cek coverage area, dan buat surat penawaran. Semua hal yang sifatnya keuangan (invoice/closing/pembayaran) jadi tanggung jawab Finance — Marketing tidak bisa lihat menu Invoice.',
                'permissions' => ['dashboard.view', 'customer.view', 'customer.create', 'quotation.view', 'quotation.create', 'package.change.view', 'package.change.create'],
            ],
            [
                'slug' => 'finance',
                'name' => 'Finance',
                'description' => 'Kelola data langganan pelanggan (paket & harga bulanan), lihat Surat Penawaran dari Marketing untuk diproses closing jadi invoice, generate tagihan bulanan, buat invoice upgrade paket, dan tandai status pembayaran invoice.',
                'permissions' => ['dashboard.view', 'customer.view', 'customer.manage', 'quotation.view', 'invoice.view', 'invoice.create', 'invoice.manage', 'package.change.view', 'package.change.approve'],
            ],
            [
                'slug' => 'reseller',
                'name' => 'Reseller',
                'description' => 'Portal khusus mitra Reseller — cuma bisa lihat pelanggan Home miliknya sendiri, tidak dapat akses dashboard internal.',
                'permissions' => ['reseller.portal.view'],
            ],
            [
                'slug' => 'hr',
                'name' => 'HR',
                'description' => 'Kelola data karyawan — tambah, edit, dan tandai resign. Tidak bisa akses ticket, pelanggan, atau modul keuangan.',
                'permissions' => ['dashboard.view', 'employee.view', 'employee.create', 'employee.manage', 'attendance.view', 'attendance.manage', 'leave.view', 'leave.manage', 'contract.template.manage'],
            ],
            [
                'slug' => 'teknisi',
                'name' => 'Teknisi',
                'description' => 'Portal khusus teknisi lapangan — penanganan work order, tiket perbaikan, dan laporan kunjungan.',
                'permissions' => ['teknisi.portal.view'],
            ],
            [
                'slug' => 'super-admin',
                'name' => 'Super Admin',
                'description' => 'Akses penuh ke seluruh modul sistem.',
                'permissions' => Permission::pluck('slug')->all(),
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                ['name' => $roleData['name'], 'description' => $roleData['description']],
            );

            $permissionIds = Permission::whereIn('slug', $roleData['permissions'])->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
    }
}
