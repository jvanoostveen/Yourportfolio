<style type="text/css" title="text/css">
    <!--
    div.center {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
    }
    -->
</style>

<div class="center">
    <video width="<?=$width?>" height="<?=$height?>" controls autoplay>
        <source src="<?=$file->basepath.$file->sysname?>" type="video/mp4">
    </video>
</div>
