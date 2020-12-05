</main>
<script src="code.js"></script>
<script>
    $(document).ready(function(){
        $( "li" ).each(function() {
            if( $(this).attr("data-page-name") === "<?php echo $PHP_SELF; ?>"){
                $(this).addClass("active-page");
            };
        });
    });
</script>
</body>
</html>