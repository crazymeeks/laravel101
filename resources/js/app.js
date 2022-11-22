import './bootstrap';
import {clickme} from './events/clickme';


const btn = document.getElementById('button');

btn.addEventListener('click', clickme);