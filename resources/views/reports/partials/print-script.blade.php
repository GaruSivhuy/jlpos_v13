<script>
    window.addEventListener('load', function () {
        window.print();
    });

    // Close the report once the print dialog is done, a tab the browser refuses to close goes back to the reports page.
    window.addEventListener('afterprint', function () {
        window.close();

        setTimeout(function () {
            window.location.href = @js(\App\Filament\Pages\Reports::getUrl());
        }, 300);
    });
</script>
