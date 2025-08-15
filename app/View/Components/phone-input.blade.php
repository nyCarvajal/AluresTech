<div>
  <input
    type="tel"
    id="{{ \$id }}"
    name="{{ \$name }}"
    value="{{ \$value }}"
    class="{{ \$class }}"
  >
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const input = document.querySelector('#{{ \$id }}');
    if (!input) return;
    window.intlTelInput(input, {
      initialCountry: '{{ \$country }}',
      separateDialCode: true,
      utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js'
    });
  });
</script>
@endpush
