/* CamKheTravel homepage interactions.
 * All user-entered text is inserted via textContent or value, never innerHTML.
 * Demo requests are NOT sent, stored in localStorage, or silently tracked.
 */
(function (root, factory) {
  'use strict';
  const api = factory();
  if (typeof module === 'object' && module.exports) module.exports = api;
  if (root && root.document) {
    root.CamKheForms = api;
    if (root.document.readyState === 'loading') root.document.addEventListener('DOMContentLoaded', () => api.init(root));
    else api.init(root);
  }
})(typeof window !== 'undefined' ? window : null, function () {
  'use strict';
  const TYPE_LABELS = Object.freeze({trip:'Bao xe / Du lịch / Đi tỉnh',partner:'Hợp tác cung cấp xe du lịch',wedding:'Xe cưới – Xe dâu',shared:'Xe ghép Hà Nội – Cẩm Khê / Yên Lập'});
  const TITLES = Object.freeze({trip:'Nhận báo giá chuyến đi',partner:'Đăng ký đối tác cung cấp xe',wedding:'Tư vấn dịch vụ xe cưới',shared:'Tư vấn chuyến xe ghép'});
  const text = value => String(value == null ? '' : value).trim();
  function normalizePhone(value) {
    let phone = text(value).replace(/[\s().-]/g, '');
    if (phone.startsWith('+84')) phone = '0' + phone.slice(3);
    else if (phone.startsWith('0084')) phone = '0' + phone.slice(4);
    else if (phone.startsWith('84')) phone = '0' + phone.slice(2);
    return /^0[35789]\d{8}$/.test(phone) ? phone : '';
  }
  function localDate(date = new Date()) {
    return date.getFullYear() + '-' + String(date.getMonth()+1).padStart(2,'0') + '-' + String(date.getDate()).padStart(2,'0');
  }
  function validDate(value) {
    if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) return false;
    const [y,m,d] = value.split('-').map(Number);
    const date = new Date(y,m-1,d,12);
    return date.getFullYear() === y && date.getMonth() === m-1 && date.getDate() === d;
  }
  function validateRequest(data, today = localDate()) {
    const errors = {};
    if (!TYPE_LABELS[data.type]) errors.type = 'Loại yêu cầu không hợp lệ.';
    if (text(data.name).length < 2 || text(data.name).length > 100) errors.name = 'Nhập họ tên từ 2 đến 100 ký tự.';
    if (!normalizePhone(data.phone)) errors.phone = 'Nhập số di động Việt Nam hợp lệ.';
    if (data.type === 'partner' && !text(data.company)) errors.company = 'Nhập tên công ty hoặc đơn vị tổ chức.';
    if (data.type !== 'partner' && !text(data.pickup)) errors.pickup = 'Nhập điểm đón.';
    if (data.type !== 'partner' && !text(data.destination)) errors.destination = 'Nhập điểm đến.';
    for (const field of ['company','pickup','destination']) if (text(data[field]).length > 180) errors[field] = 'Nội dung vượt quá 180 ký tự.';
    if (text(data.passengers).length > 80) errors.passengers = 'Thông tin số khách vượt quá 80 ký tự.';
    if (text(data.notes).length > 2000) errors.notes = 'Yêu cầu thêm vượt quá 2.000 ký tự.';
    const departure = text(data.departure), returnDate = text(data.returnDate);
    if (departure && (!validDate(departure) || departure < today)) errors.departure = 'Ngày đi phải hợp lệ và không trước hôm nay.';
    if (returnDate && (!departure || !validDate(returnDate) || returnDate < departure)) errors.returnDate = 'Chọn ngày đi trước; ngày về không được trước ngày đi.';
    if (data.consent !== true) errors.consent = 'Cần đồng ý cung cấp thông tin trước khi tiếp tục.';
    if (text(data.website)) errors.website = 'Không thể tiếp nhận yêu cầu này.';
    return errors;
  }
  function formatDate(value) { return validDate(text(value)) ? value.split('-').reverse().join('/') : 'Chưa xác định'; }
  function buildSummary(data) {
    const rows = [
      'YÊU CẦU TƯ VẤN – CAMKHETRAVEL',
      'Dịch vụ: ' + (text(data.service) || TYPE_LABELS[data.type] || 'Tư vấn xe'),
      'Họ tên: ' + text(data.name),
      'Điện thoại: ' + normalizePhone(data.phone)
    ];
    if (text(data.company)) rows.push('Đơn vị: ' + text(data.company));
    rows.push('Điểm đón: ' + (text(data.pickup) || 'Trao đổi thêm'), 'Điểm đến: ' + (text(data.destination) || 'Trao đổi thêm'));
    rows.push('Ngày đi: ' + formatDate(data.departure));
    if (text(data.returnDate)) rows.push('Ngày về: ' + formatDate(data.returnDate));
    rows.push('Loại xe: ' + (text(data.vehicle) || 'Cần tư vấn'), 'Số khách / hành lý: ' + (text(data.passengers) || 'Trao đổi thêm'));
    if (text(data.notes)) rows.push('Yêu cầu thêm: ' + text(data.notes));
    rows.push('', 'Đây là yêu cầu tư vấn, chưa phải xác nhận đặt xe.');
    return rows.join('\n');
  }
  function isSameOriginEndpoint(endpoint, origin) {
    try { const url = new URL(endpoint, origin); return ['http:','https:'].includes(url.protocol) && url.origin === origin; }
    catch (_) { return false; }
  }
  function init(win) {
    const doc = win.document;
    const configElement = doc.querySelector('[data-camkhe-config]');
    const config = Object.assign({phone:'',zaloUrl:'',email:'',region:'Cẩm Khê, Phú Thọ – phục vụ theo lịch trình',leadEndpoint:''}, configElement ? {
      phone: configElement.dataset.phone,
      zaloUrl: configElement.dataset.zaloUrl,
      email: configElement.dataset.email,
      region: configElement.dataset.region,
      leadEndpoint: configElement.dataset.leadEndpoint
    } : {});
    const $ = selector => doc.querySelector(selector);
    const $$ = selector => Array.from(doc.querySelectorAll(selector));
    const form = $('#requestForm');
    if (!form || !win.bootstrap) return;
    const modalElement = $('#quoteModal');
    const quoteModal = win.bootstrap.Modal.getOrCreateInstance(modalElement);
    const vehicleModalElement = $('#vehicleModal');
    const vehicleModal = win.bootstrap.Modal.getOrCreateInstance(vehicleModalElement);
    const requestResult = $('#requestResult');
    const errorBox = $('#formError');
    const submitButton = $('#requestSubmit');
    const isDemo = !text(config.leadEndpoint);
    let submitting = false;
    let selectedVehicle = null;
    let pendingController = null;
    const today = localDate();
    ['#quickDate','#requestDeparture','#requestReturn'].forEach(s => { $(s).min = today; });
    $$('[data-year]').forEach(el => { el.textContent = String(new Date().getFullYear()); });
    if (text(config.region)) $$('[data-company-region]').forEach(el => { el.textContent = config.region; });
    const phone = normalizeContactPhone(config.phone);
    if (phone) $$('[data-phone-label]').forEach(el => { el.textContent = phone.replace(/(\d{4})(\d{3})(\d{3})/, '$1 $2 $3'); });
    if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(text(config.email))) {
      $('[data-email-row]').hidden = false;
      $('[data-email-link]').textContent = config.email;
      $('[data-email-link]').href = 'mailto:' + config.email;
    }
    if (!isDemo) {
      $('#demoNotice').hidden = true;
      $('[data-submit-label]').textContent = 'Gửi yêu cầu tư vấn';
      $('#privacyDataText').textContent = 'Khi gửi form, thông tin được gửi về website và lưu thành yêu cầu liên hệ để CamKheTravel xử lý. Việc gửi yêu cầu chưa xác nhận chuyến xe, lịch trình hoặc giá.';
    }
    function clearValidity() { Array.from(form.elements).forEach(el => { if (el.setCustomValidity) el.setCustomValidity(''); }); }
    function openQuote(options = {}) {
      if (submitting) return;
      const type = TYPE_LABELS[options.type] ? options.type : 'trip';
      const nav = $('#mainNav');
      if (nav.classList.contains('show')) win.bootstrap.Collapse.getOrCreateInstance(nav).hide();
      form.reset(); clearValidity(); form.classList.remove('was-validated');
      form.hidden = false; requestResult.hidden = true; errorBox.hidden = true; errorBox.textContent = '';
      $('#copyStatus').textContent = '';
      $('#demoNotice').hidden = !isDemo;
      $('#demoNotice').textContent = isDemo ? 'Bản xem trước chưa kết nối máy chủ.' : 'Gửi form để CamKheTravel tiếp nhận yêu cầu tư vấn. Đây chưa phải xác nhận đặt xe.';
      $('#requestType').value = type;
      $('#requestService').value = text(options.service);
      $('#requestServiceId').value = text(options.serviceId);
      $('#quoteTitle').textContent = TITLES[type];
      $('#companyGroup').hidden = type !== 'partner';
      $('#requestCompany').disabled = type !== 'partner';
      $('#requestCompany').required = type === 'partner';
      $('#requestPickup').required = type !== 'partner';
      $('#requestDestination').required = type !== 'partner';
      $('#requestDeparture').min = localDate();
      $('#requestReturn').min = localDate();
      if (type === 'wedding') $('#requestVehicle').value = 'Xe cưới';
      if (options.vehicle) $('#requestVehicle').value = options.vehicle;
      if (type === 'shared') { $('#requestPickup').value = 'Cẩm Khê / Yên Lập'; $('#requestDestination').value = 'Hà Nội'; }
      const prefill = options.prefill || {};
      Object.keys(prefill).forEach(key => {
        const input = form.elements.namedItem(key);
        if (input && ['pickup','destination','departure','passengers','notes'].includes(key)) input.value = text(prefill[key]);
      });
      $('#requestSummary').value = '';
      quoteModal.show();
    }
    modalElement.addEventListener('shown.bs.modal', () => { if (!form.hidden) $('#requestName').focus({preventScroll:true}); });
    modalElement.addEventListener('hidden.bs.modal', () => {
      if (pendingController) pendingController.abort();
      // Do not leave personal details in a closed demo dialog.
      form.reset(); $('#requestSummary').value = ''; requestResult.hidden = true; form.hidden = false;
    });
    $$('[data-quote-type]').forEach(button => button.addEventListener('click', () => openQuote({type:button.dataset.quoteType, service:button.dataset.service || '', serviceId:button.dataset.serviceId || ''})));
    $('#quickQuote').addEventListener('submit', event => {
      event.preventDefault();
      if (!event.currentTarget.reportValidity()) return;
      openQuote({type:'trip',prefill:Object.fromEntries(new FormData(event.currentTarget))});
    });
    $$('[data-vehicle]').forEach(button => button.addEventListener('click', () => {
      selectedVehicle = {code:button.dataset.vehicle, name:button.dataset.vehicleName, image:button.dataset.vehicleImage, description:button.dataset.vehicleDescription};
      if (!selectedVehicle.name) return;
      $('#vehicleTitle').textContent = selectedVehicle.name;
      $('#vehicleDetailImage').src = selectedVehicle.image;
      $('#vehicleDetailImage').alt = selectedVehicle.name + ' – ảnh mặc định';
      $('#vehicleDetailDescription').textContent = selectedVehicle.description;
      vehicleModal.show();
    }));
    $('#quoteVehicle').addEventListener('click', () => {
      const choice = selectedVehicle;
      vehicleModalElement.addEventListener('hidden.bs.modal', () => openQuote({type:'trip',vehicle:choice ? choice.name : 'Cần tư vấn'}), {once:true});
      vehicleModal.hide();
    });
    $$('[data-contact]').forEach(button => button.addEventListener('click', () => {
      const channel = button.dataset.contact;
      if (channel === 'zalo' && /^https:\/\//i.test(text(config.zaloUrl))) {
        win.open(text(config.zaloUrl), '_blank', 'noopener,noreferrer');
        return;
      }
      const targetPhone = channel === 'zalo' ? normalizePhone(config.zaloUrl || config.phone) : phone;
      if (!targetPhone) {
        openQuote({type:'trip'});
        $('#demoNotice').hidden = false;
        $('#demoNotice').textContent = (channel === 'zalo' ? 'Zalo' : 'Số điện thoại') + ' chưa được cấu hình. Anh/chị có thể gửi yêu cầu tư vấn bằng form bên dưới.';
        return;
      }
      if (channel === 'zalo') win.open('https://zalo.me/' + targetPhone,'_blank','noopener,noreferrer');
      else win.location.href = 'tel:+84' + targetPhone.slice(1);
    }));
    $('#requestDeparture').addEventListener('change', () => { $('#requestReturn').min = $('#requestDeparture').value || localDate(); });
    form.addEventListener('input', event => { if (event.target.setCustomValidity) event.target.setCustomValidity(''); errorBox.hidden = true; });
    form.addEventListener('change', event => { if (event.target.setCustomValidity) event.target.setCustomValidity(''); errorBox.hidden = true; });
    form.addEventListener('submit', async event => {
      event.preventDefault(); if (submitting) return;
      const data = Object.fromEntries(new FormData(form));
      data.consent = $('#requestConsent').checked;
      data.type = $('#requestType').value;
      clearValidity();
      const errors = validateRequest(data);
      Object.entries(errors).forEach(([key,value]) => { const field = form.elements.namedItem(key); if (field && field.setCustomValidity) field.setCustomValidity(value); });
      form.classList.add('was-validated');
      if (Object.keys(errors).length || !form.checkValidity()) {
        errorBox.textContent = Object.values(errors).slice(0,3).join('\n') || 'Vui lòng kiểm tra các trường thông tin.';
        errorBox.hidden = false;
        const invalid = form.querySelector(':invalid:not([type="hidden"]):not([tabindex="-1"])');
        if (invalid) invalid.focus();
        return;
      }
      data.phone = normalizePhone(data.phone);
      data.source = 'camkhetravel_homepage';
      submitting = true; submitButton.disabled = true;
      $('[data-submit-label]').textContent = isDemo ? 'Đang tạo nội dung…' : 'Đang gửi yêu cầu…';
      let timeoutId;
      try {
        if (!isDemo) {
          if (!isSameOriginEndpoint(config.leadEndpoint, win.location.origin)) throw new Error('Endpoint phải cùng tên miền với website. Chưa gửi thông tin.');
          pendingController = new AbortController();
          timeoutId = win.setTimeout(() => pendingController && pendingController.abort(), 20000);
          const csrf = doc.querySelector('meta[name="csrf-token"]')?.content || config.csrfToken;
          const headers = {'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest'};
          if (csrf) headers['X-CSRF-TOKEN'] = csrf;
          const response = await win.fetch(config.leadEndpoint, {method:'POST', headers, credentials:'same-origin', body:JSON.stringify(data), signal:pendingController.signal});
          const result = await response.json().catch(() => null);
          if (!response.ok || !result || result.success !== true) {
            const serverError = result && typeof result.message === 'string' ? result.message.slice(0,400) : 'Hệ thống chưa xác nhận tiếp nhận yêu cầu. Vui lòng thử lại hoặc liên hệ trực tiếp.';
            throw new Error(serverError);
          }
        }
        if (!modalElement.classList.contains('show')) return;
        $('#requestSummary').value = buildSummary(data);
        $('#resultTitle').textContent = isDemo ? 'Đã tạo nội dung yêu cầu demo' : 'Đã gửi yêu cầu tư vấn';
        $('#resultDescription').textContent = isDemo ? 'Chưa gửi thông tin đến nhà xe. Có thể sao chép nội dung để gửi qua kênh liên hệ chính thức.' : 'Hệ thống đã xác nhận tiếp nhận yêu cầu. Đây chưa phải xác nhận đặt xe; nhà xe cần liên hệ để thống nhất lịch trình và chi phí.';
        form.hidden = true; requestResult.hidden = false;
        requestResult.scrollIntoView({block:'nearest'});
        $('#copySummary').focus({preventScroll:true});
      } catch (error) {
        errorBox.textContent = error.name === 'AbortError' ? 'Chưa nhận được xác nhận từ hệ thống. Vui lòng kiểm tra lại trước khi gửi thêm lần nữa.' : error.message || 'Không gửi được yêu cầu. Vui lòng thử lại.';
        errorBox.hidden = false;
      } finally {
        win.clearTimeout(timeoutId); pendingController = null; submitting = false; submitButton.disabled = false;
        $('[data-submit-label]').textContent = isDemo ? 'Tạo yêu cầu demo' : 'Gửi yêu cầu tư vấn';
      }
    });
    $('#editRequest').addEventListener('click', () => { requestResult.hidden = true; form.hidden = false; $('#requestName').focus(); });
    $('#copySummary').addEventListener('click', async () => {
      const content = $('#requestSummary');
      try {
        if (!win.navigator.clipboard || !win.isSecureContext) throw new Error('Clipboard unavailable');
        await win.navigator.clipboard.writeText(content.value);
        $('#copyStatus').textContent = 'Đã sao chép nội dung.';
      } catch (_) {
        content.focus(); content.select();
        let copied = false; try { copied = doc.execCommand('copy'); } catch (_) { /* The text remains selected. */ }
        $('#copyStatus').textContent = copied ? 'Đã sao chép nội dung.' : 'Nội dung đã được chọn. Nhấn Ctrl+C hoặc chọn Sao chép trên điện thoại.';
      }
    });
    const nav = $('#mainNav');
    $$('#mainNav .nav-link').forEach(link => link.addEventListener('click', () => { if (nav.classList.contains('show')) win.bootstrap.Collapse.getOrCreateInstance(nav).hide(); }));
    const navLinks = $$('#mainNav .nav-link');
    function updateScroll() {
      const y = win.scrollY;
      $('#siteHeader').classList.toggle('is-scrolled',y>12);
      $('#backTop').hidden = y<650;
      let current = 'trang-chu';
      navLinks.forEach(link => {
        const target = doc.getElementById(link.hash.slice(1));
        if (target && target.getBoundingClientRect().top <= 140) current = target.id;
      });
      navLinks.forEach(link => {
        const active = link.hash === '#' + current;
        link.classList.toggle('active',active);
        if (active) link.setAttribute('aria-current','location'); else link.removeAttribute('aria-current');
      });
    }
    let scrollQueued = false;
    win.addEventListener('scroll', () => { if (!scrollQueued) { scrollQueued = true; win.requestAnimationFrame(() => { updateScroll(); scrollQueued = false; }); } }, {passive:true});
    $('#backTop').addEventListener('click', () => win.scrollTo({top:0,behavior:win.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'}));
    updateScroll();
  }
  function normalizeContactPhone(value) {
    let phone = text(value).replace(/\D/g, '');
    if (phone.startsWith('0084')) phone = '0' + phone.slice(4);
    else if (phone.startsWith('84')) phone = '0' + phone.slice(2);
    return /^0\d{9,10}$/.test(phone) ? phone : '';
  }
  return { normalizePhone, localDate, validateRequest, buildSummary, isSameOriginEndpoint, init };
});
