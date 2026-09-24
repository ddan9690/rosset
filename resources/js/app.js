import './bootstrap';

import { createIcons, icons } from 'lucide';
import AOS from 'aos';
import 'aos/dist/aos.css';

import Swal from 'sweetalert2';
window.Swal = Swal;

createIcons({ icons });

AOS.init({
    duration: 700,
    once: true,
});