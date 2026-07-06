import './bootstrap';

import './checkout/index';

import './products/variants';

import './dashboard/index';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

Alpine.start()
