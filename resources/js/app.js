import './bootstrap';

// 以下为新增部分
import '../sass/app.scss'
import * as bootstrap from 'bootstrap'

// 此处需在引入 Vue 之后引入
import './components/SelectDistrict';
import './components/UserAddressesCreateAndEdit';
const app = new Vue({
    el: '#app'
});