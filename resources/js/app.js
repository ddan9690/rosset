import './bootstrap';

import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import AOS from 'aos';
import 'aos/dist/aos.css';

window.Alpine = Alpine;

Alpine.start();


createIcons({ icons });


AOS.init({
    duration: 700,
    once: true,
});
