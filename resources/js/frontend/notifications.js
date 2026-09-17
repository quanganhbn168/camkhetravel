import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
const toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 5000, timerProgressBar: true });
export const notifications = {
    loading() { toast.fire({ title: 'Đang gửi thông tin…', timer: false, didOpen: () => Swal.showLoading() }); },
    success(text) { toast.fire({ icon: 'success', title: 'Đã nhận thông tin', text, timer: 5000 }); },
    error(text) { toast.fire({ icon: 'error', title: 'Gửi chưa thành công', text, timer: 7000 }); },
};
