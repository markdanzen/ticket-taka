<?php 
/* Template Name: Page Dropdown */ 
get_header();
?>

<?php
// Fetch categories and their subcategories
$categories = get_categories(array(
    'hide_empty' => 0,
    'taxonomy' => 'category',
    'parent' => 0
));
?>

<select id="category-dropdown">
    <option value="">Select Category</option>
    <?php foreach ($categories as $category) : ?>
        <optgroup label="<?php echo $category->name; ?>">
            <?php
            $subcategories = get_categories(array(
                'hide_empty' => 0,
                'taxonomy' => 'category',
                'parent' => $category->term_id
            ));
            foreach ($subcategories as $subcategory) : ?>
                <option value="<?php echo $subcategory->term_id; ?>"><?php echo $subcategory->name; ?></option>
            <?php endforeach; ?>
        </optgroup>
    <?php endforeach; ?>
</select>

<div id="custom-pages">
    <!-- Filtered custom pages will be displayed here -->
</div>


<?php get_footer(); ?>
