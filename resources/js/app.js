import './bootstrap';

import Alpine from 'alpinejs';
import mask from '@alpinejs/mask';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal   = Swal;

// Register Alpine.js plugins
Alpine.plugin(mask);

// ============================================================
// Swal global konfigurasi default
// ============================================================
window.SwalKonfirm = function(options = {}) {
    return Swal.fire({
        title:              options.title       ?? 'Yakin?',
        text:               options.text        ?? 'Data yang dihapus tidak dapat dikembalikan.',
        icon:               options.icon        ?? 'warning',
        iconColor:          options.iconColor   ?? '#EF4444',
        showCancelButton:   true,
        confirmButtonText:  options.confirmText ?? 'Ya, Hapus',
        cancelButtonText:   options.cancelText  ?? 'Batal',
        confirmButtonColor: options.confirmColor ?? '#EF4444',
        cancelButtonColor:  '#6B7280',
        reverseButtons:     true,
        focusCancel:        true,
        customClass: {
            popup:         'swal-popup-custom',
            title:         'swal-title-custom',
            confirmButton: 'swal-confirm-custom',
            cancelButton:  'swal-cancel-custom',
        },
    });
};

// ============================================================
// Helper: intercept form submit dengan SwalKonfirm
// Cara pakai: <form data-confirm="Pesan konfirmasi" ...>
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('submit', async function(e) {
        const form    = e.target;
        const message = form.dataset.confirm;

        if (! message) return; // tidak ada atribut, lanjutkan biasa

        e.preventDefault();

        const result = await SwalKonfirm({ text: message });
        if (result.isConfirmed) {
            form.removeAttribute('data-confirm'); // hindari loop
            form.submit();
        }
    }, true);
});

Alpine.start();
