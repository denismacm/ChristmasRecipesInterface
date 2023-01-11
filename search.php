<?php
    require 'config/config.php';
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ( $mysqli->connect_errno ) {
		echo $mysqli->connect_error;
		exit();
	}

    $results_food_type = $mysqli->query("SELECT * FROM food_type;");

    if ( !$results_food_type ) {
		echo $mysqli->error;
		$mysqli->close();
		exit();
	}

    $mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Search Form | Christmas Recipes</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div id="header">
            <h2>Christmas Recipes for <span id="strike">the Holiday Season</span> Any Season!</h2>
        </div> <!-- #header -->

        <div id="navbar">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="search.php">Search for Recipes</a></li>
                <li><a href="add_recipe.php">Add a Recipe</a></li>
                <li><a href="contact_me.php">Contact Me</a></li>
            </ul>
        </div> <!-- #navbar -->

        <div id="container">
            <div id="center-box">
                <div class="title">Search for your favorite Christmas recipes!</div>
                <br>
                <form id="search-form" action="search_results.php" method="GET">
                    <label for="recipe_name">Name of Recipe:</label>
                    <input type="text" id="recipe_name" name="recipe_name" placeholder="e.g. cookies, pies, etc...">

                    <label for="author_name">Name of Recipe's Author:</label>
                    <input type="text" id="author_name" name="author_name" placeholder="e.g. John Doe, Tommy Trojan, etc...">

                    <label for="food_type_id">Food Type:</label>
                    <select id="food_type_id" name="food_type_id">
                        <option value="" selected>-- All --</option>

                        <?php while ( $row = $results_food_type->fetch_assoc() ) : ?>
							<option value="<?php echo $row['food_type_id']; ?>">
								<?php echo $row['food_type_name']; ?>
							</option>
						<?php endwhile; ?>
                    </select>

                    <div id="center-flex-box">
                        <div id="sort-by">
                            Sort by: <br>
                            <input type="radio" id="recipe_name_sort" name="sort_by" value="recipe_name_sort">
                            <label for="recipe_name_sort">Name of Recipe</label><br>
                            <input type="radio" id="recipe_author_sort" name="sort_by" value="recipe_author_sort">
                            <label for="recipe_author_sort">Name of Recipe's Author</label><br>
                            <input type="radio" id="food_type_sort" name="sort_by" value="food_type_sort">
                            <label for="food_type_sort">Food Type</label><br><br>
                        </div> <!-- #sort-by -->

                        <div id="sort-order">
                            Sort order: <br>
                            <input type="radio" id="ascending" name="radio-sort-order" value="ascending">
                            <label for="ascending">Ascending</label><br>
                            <input type="radio" id="descending" name="radio-sort-order" value="descending">
                            <label for="descending">Descending</label><br>
                            <input type="radio" id="random" name="radio-sort-order" value="random">
                            <label for="random">Random</label><br><br>
                        </div> <!-- #sort-order -->
                    </div> <!-- #center-flex-box -->

                    <input id="submit-btn" type="submit" value="Search">
                </form> <!-- #search-form-->
            </div> <!-- #center-box -->
        </div> <!-- #container -->

        <div id="footer">
            Website created by Denis Mac.
        </div> <!-- #footer -->
    </body>
</html>