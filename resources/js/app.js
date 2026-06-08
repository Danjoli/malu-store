import './bootstrap';

import './payments/card';
import './payments/pix';
import './payments/boleto';

import './checkout/index';

import './products/variants';

import './dashboard/index';

import Alpine from 'alpinejs'

window.Alpine = Alpine

Alpine.start()
