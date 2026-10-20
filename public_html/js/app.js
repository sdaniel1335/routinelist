var App = {
  init: function () {
    this.bindUIActions();
    this.updateRuleFields();
    // Keep today's marker current across Budapest midnight.
    this.watchDate();
  },
  bindUIActions: function () {
    document.addEventListener('submit', function (event) {
      var form = event.target;
      if (form.hasAttribute('data-confirm') && !window.confirm(form.getAttribute('data-confirm'))) {
        event.preventDefault();
      }
    });
    var rule = document.getElementById('routine-rule');
    if (rule) { rule.addEventListener('change', function () { App.updateRuleFields(); }); }
  },
  updateRuleFields: function () {
    var rule = document.getElementById('routine-rule');
    if (!rule) { return; }
    document.querySelectorAll('[data-rules]').forEach(function (field) {
      var visible = field.getAttribute('data-rules').split(' ').indexOf(rule.value) !== -1;
      field.hidden = !visible;
      field.querySelectorAll('input, select').forEach(function (input) { input.disabled = !visible; input.required = visible; });
    });
    var weekday = document.getElementById('routine-weekday');
    weekday.options[0].disabled = rule.value !== 'quarterly';
    if (rule.value !== 'quarterly' && weekday.value === '0') { weekday.value = '1'; }
    var day = document.getElementById('routine-day');
    day.min = rule.value === 'halfyear' ? '0' : '1';
    if (rule.value !== 'halfyear' && day.value === '0') { day.value = '1'; }
    var month = document.getElementById('routine-month');
    month.max = rule.value === 'halfyear' ? '6' : '12';
    if (rule.value === 'halfyear' && Number(month.value) > 6) { month.value = '1'; }
  },
  watchDate: function () {
    if (!document.querySelector('.week-grid')) { return; }
    var formatter = new Intl.DateTimeFormat('en-CA', { timeZone: 'Europe/Budapest', year: 'numeric', month: '2-digit', day: '2-digit' });
    var initial = formatter.format(new Date());
    window.setInterval(function () {
      if (formatter.format(new Date()) !== initial) {
        var parts = formatter.formatToParts(new Date());
        var values = {};
        parts.forEach(function (part) { values[part.type] = part.value; });
        var date = values.year + '-' + values.month + '-' + values.day;
        document.querySelectorAll('.day:not(.day-outside)').forEach(function (column) {
          var current = column.querySelector('time').getAttribute('datetime') === date;
          column.classList.toggle('is-today', current);
          column.removeAttribute('aria-current');
          var badge = column.querySelector('.today-badge');
          if (badge) { badge.remove(); }
          if (current) {
            column.setAttribute('aria-current', 'date');
            badge = document.createElement('strong');
            badge.className = 'today-badge';
            badge.textContent = 'MA';
            column.querySelector('.day-heading').prepend(badge);
          }
        });
        var form = document.getElementById('routine-editor');
        if (!form || !form.dataset.dirty) { window.location.reload(); }
      }
    }, 30000);
    document.querySelectorAll('form').forEach(function (form) {
      form.addEventListener('input', function () { var editor = document.getElementById('routine-editor'); if (editor) { editor.dataset.dirty = '1'; } });
    });
  }
};
