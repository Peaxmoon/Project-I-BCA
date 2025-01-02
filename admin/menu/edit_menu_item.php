<!-- Inside the form where the current image is displayed -->
<label>Current Image:</label>
<?php 
    // Construct the correct image path
    $imagePath = "/Project-I-BCA/assets/images/" . htmlspecialchars($menu_item['image']);
?>
<img src="<?php echo $imagePath; ?>" alt="Current Image" class="current-image">

<style>
    .current-image {
        max-width: 200px;
        height: auto;
        margin: 10px 0;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style> 