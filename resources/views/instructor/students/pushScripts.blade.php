@push('scripts')
<script>
    $(document).ready(function() {
        // Filter form submission
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            // Submit via AJAX or regular form submission
            this.submit();
        });
    });
</script>
@endpush

