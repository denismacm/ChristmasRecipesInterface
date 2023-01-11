<?php
    require 'config/config.php';
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ( $mysqli->connect_errno ) {
		echo $mysqli->connect_error;
		exit();
    }
    $mysqli->set_charset('utf8');

    $sql_search_results = "SELECT recipe.recipe_id AS recipe_id, recipe.image_url AS image_url, recipe.recipe_name AS recipe_name, author.author_name AS author_name, food_type.food_type_name AS food_type_name
                            FROM recipe
                                LEFT JOIN author
                                    ON recipe.author_id = author.author_id
                                LEFT JOIN food_type
                                    ON recipe.food_type_id = food_type.food_type_id
                            WHERE 1=1";
    if ( isset($_GET['recipe_name']) && trim($_GET['recipe_name']) != '') {
        $recipe_name = $mysqli->real_escape_string(trim($_GET['recipe_name']));
        $sql_search_results = $sql_search_results . " AND recipe.recipe_name LIKE '%$recipe_name%'";
    }
    if ( isset($_GET['author_name']) && trim($_GET['author_name']) != '') {
        $author_name = $mysqli->real_escape_string(trim($_GET['author_name']));
        $sql_search_results = $sql_search_results . " AND author.author_name LIKE '%$author_name%'";
    }
    if ( isset($_GET['food_type_id']) && trim($_GET['food_type_id']) != '') {
        $food_type_id = trim($_GET['food_type_id']);
        $sql_search_results = $sql_search_results . " AND food_type.food_type_id = $food_type_id";
    }

    if ( isset($_GET['radio-sort-order']) && trim($_GET['radio-sort-order']) == 'random' ) {
        $sql_search_results = $sql_search_results . " ORDER BY RAND()";
    } else {
        if ( isset($_GET['sort_by']) && trim($_GET['sort_by']) != '') {
            if (trim($_GET['sort_by']) == 'recipe_name_sort') {
                $sql_search_results = $sql_search_results . " ORDER BY recipe.recipe_name";
            } else if (trim($_GET['sort_by']) == 'recipe_author_sort') {
                $sql_search_results = $sql_search_results . " ORDER BY author.author_name";
            } else if (trim($_GET['sort_by']) == 'food_type_sort') {
                $sql_search_results = $sql_search_results . " ORDER BY food_type.food_type_name";
            }

            if (isset($_GET['radio-sort-order'])) {
                if (trim($_GET['radio-sort-order']) == 'ascending') {
                    $sql_search_results = $sql_search_results . " ASC";
                } else if (trim($_GET['radio-sort-order']) == 'descending') {
                    $sql_search_results = $sql_search_results . " DESC";
                }
            }       
        }
    }

    $sql_search_results = $sql_search_results . ";";

    // echo $sql_search_results;

    $search_results = $mysqli->query($sql_search_results);

    if ( !$search_results ) {
		echo $mysqli->error;
		$mysqli->close();
		exit();
	}

    $results_food_type = $mysqli->query("SELECT * FROM food_type;");

    if ( !$results_food_type ) {
		echo $mysqli->error;
		$mysqli->close();
		exit();
	}

    $per_page = 10;
	$total = $search_results->num_rows;
	$final_page = ceil($total / $per_page);

    if ( isset($_GET['page_num']) && trim($_GET['page_num']) != '') {
		$current_page_num = $_GET['page_num'];
	} else {
		$current_page_num = 1;
	}

    if ($current_page_num < 1 || $current_page_num > $final_page) {
		$current_page_num = 1;
	}

	$beginning_index = ($current_page_num - 1) * $per_page;

    $sql_search_results = rtrim($sql_search_results, ';');

    $sql_search_results = $sql_search_results . " LIMIT $beginning_index, $per_page;";

    $results_for_page = $mysqli->query($sql_search_results);

    if ( !$results_for_page ) {
		echo $mysqli->error;
		$mysqli->close();
		exit();
	}

    $mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Search Results | Christmas Recipes</title>
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

        <div id="container-long">
            <div id="center-box">
                <div class="title">Search for your favorite Christmas recipes!</div>
                <br>
                <form id="search-form" action="search_results.php" method="GET">
                    <label for="recipe_name">Name of Recipe:</label>
                    <input type="text" id="recipe_name" name="recipe_name" placeholder="e.g. cookies, pies, etc..." value="<?php if (isset($recipe_name)) echo trim($recipe_name);?>">

                    <label for="author_name">Name of Recipe's Author:</label>
                    <input type="text" id="author_name" name="author_name" placeholder="e.g. John Doe, Tommy Trojan, etc..." value="<?php if (isset($author_name)) echo trim($author_name);?>">

                    <label for="food_type_id">Food Type:</label>
                    <select id="food_type_id" name="food_type_id">
                        <?php if (!isset($food_type_id)) : ?>
                            <option value="" selected>-- All --</option>
                        <?php else :?>
                            <option value="">-- All --</option>
                        <?php endif; ?>

                        <?php while ( $row = $results_food_type->fetch_assoc() ) : ?>
							<option value="<?php echo $row['food_type_id']; ?>" 
                                <?php if (isset($food_type_id) && trim($food_type_id) == $row['food_type_id']) : ?>
                                    selected
                                <?php endif; ?>
                            >
								<?php echo $row['food_type_name']; ?>
							</option>
						<?php endwhile; ?>
                    </select>

                    <div id="center-flex-box">
                        <div id="sort-by">
                            Sort by: <br>
                            <input type="radio" id="recipe_name_sort" name="sort_by" value="recipe_name_sort" 
                            <?php if (isset($_GET['sort_by']) && trim($_GET['sort_by']) == 'recipe_name_sort') : ?>
                                checked="checked"
                            <?php endif; ?>>
                            <label for="recipe_name_sort">Name of Recipe</label><br>
                            <input type="radio" id="recipe_author_sort" name="sort_by" value="recipe_author_sort"
                            <?php if (isset($_GET['sort_by']) && trim($_GET['sort_by']) == 'recipe_author_sort') : ?>
                                checked="checked"
                            <?php endif; ?>>
                            <label for="recipe_author_sort">Name of Recipe's Author</label><br>
                            <input type="radio" id="food_type_sort" name="sort_by" value="food_type_sort"
                            <?php if (isset($_GET['sort_by']) && trim($_GET['sort_by']) == 'food_type_sort') : ?>
                                checked="checked"
                            <?php endif; ?>>
                            <label for="food_type_sort">Food Type</label><br><br>
                        </div> <!-- #sort-by -->

                        <div id="sort-order">
                            Sort order: <br>
                            <input type="radio" id="ascending" name="radio-sort-order" value="ascending"
                            <?php if (isset($_GET['radio-sort-order']) && trim($_GET['radio-sort-order']) == 'ascending') : ?>
                                checked="checked"
                            <?php endif; ?>>
                            <label for="ascending">Ascending</label><br>
                            <input type="radio" id="descending" name="radio-sort-order" value="descending"
                            <?php if (isset($_GET['radio-sort-order']) && trim($_GET['radio-sort-order']) == 'descending') : ?>
                                checked="checked"
                            <?php endif; ?>>
                            <label for="descending">Descending</label><br>
                            <input type="radio" id="random" name="radio-sort-order" value="random"
                            <?php if (isset($_GET['radio-sort-order']) && trim($_GET['radio-sort-order']) == 'random') : ?>
                                checked="checked"
                            <?php endif; ?>>
                            <label for="random">Random</label><br><br>
                        </div> <!-- #sort-order -->
                    </div> <!-- #center-flex-box -->

                    <input id="submit-btn" type="submit" value="Search">
                </form> <!-- #search-form-->
            </div> <!-- #center-box -->

            <div id="search-results-box">
                <div class="title">Search Results</div>
                <div class="title">Showing <?php echo $results_for_page->num_rows;?> results out of <?php echo $total;?>.</div>
                <div class="pagination">
                    <?php if ($current_page_num <= 1) :?>
                        First
                    <?php else : ?>
                        <a href="<?php
                                    $_GET['page_num'] = 1;
                                    echo $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET);
                                ?>">First</a>
                    <?php endif; ?>
                    <?php if ($current_page_num <= 1) : ?>
                        Previous
                    <?php else : ?>
                        <a href="<?php
								$_GET['page_num'] = $current_page_num - 1;
								echo $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET);
							?>">Previous</a>
                    <?php endif; ?>
                    <a href=""><?php echo $current_page_num; ?></a>
                    <?php if ($current_page_num >= $final_page) : ?>
                        Next
                    <?php else : ?>
                        <a href="<?php
								$_GET['page_num'] = $current_page_num + 1;
								echo $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET);
							?>">Next</a>
                    <?php endif; ?>
                    <?php if ($current_page_num >= $final_page) : ?>
                        Last
                    <?php else : ?>
                        <a href="<?php
								$_GET['page_num'] = $final_page;
								echo $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET);
							?>">Last</a>
                    <?php endif; ?>
                </div> <!-- .pagination -->
                <br>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Author</th>
                            <th>Food Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $results_for_page->fetch_assoc()) : ?>
                            <tr>
                                <td>
                                    <?php if ($row['image_url'] == null || $row['image_url'] == '') : ?>
                                        No image available.
                                    <?php else : ?>
                                        <img class="search-img" src="<?php echo $row['image_url'];?>" alt="Picture of <?php echo $row['recipe_name'];?>">
                                    <?php endif; ?>
                                </td>
                                <td><a href="recipe_details.php?recipe_id=<?php echo $row['recipe_id']; ?>"><?php echo $row['recipe_name'];?></a></td>
                                <td><?php echo $row['author_name'];?></td>
                                <td><?php echo $row['food_type_name'];?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div> <!-- #search-results-box -->
        </div> <!-- #container -->

        <div id="footer">
            Website created by Denis Mac.
        </div> <!-- #footer -->
    </body>
</html>