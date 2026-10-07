export interface User {
    id: number;
    name: string;
    email: string;
    role: string;
}

export interface FlashMessages {
    success?: string | null;
    error?: string | null;
}

export interface PageProps<T = Record<string, unknown>> {
    auth: {
        user: User | null;
    };
    flash: FlashMessages;
    errors: Record<string, string>;
    [key: string]: unknown;
}

export interface Employee {
    id: number;
    user_id?: number | null;
    nama_lengkap: string;
    jabatan: string;
    nik: string;
    email: string;
    no_hp?: string | null;
    alamat?: string | null;
    no_jmo?: string | null;
    no_kpj?: string | null;
    photo_path?: string | null;
    tahun_masuk?: number | null;
    tanggal_keluar?: string | null;
    status_karyawan: 'aktif' | 'nonaktif';
    created_at?: string;
    updated_at?: string;
}
