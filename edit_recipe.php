<?php
    if (!isset($_GET['recipe_id']) || trim($_GET['recipe_id']) == '') {
        $error = "Error in URL retrieving recipe.";
    } else {
        require 'config/config.php';
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ( $mysqli->connect_errno ) {
            echo $mysqli->connect_error;
            exit();
        }
        $mysqli->set_charset('utf8');

        $recipe_id = trim($_GET['recipe_id']);

        $sql_recipe_details = "SELECT recipe.recipe_id AS recipe_id, recipe.recipe_name AS recipe_name, recipe.url AS url, recipe.image_url AS image_url, recipe.description AS description, author.author_name AS author_name, author.author_id AS author_id, recipe.ingredients AS ingredients, recipe.method AS method, food_type.food_type_name AS food_type_name, food_type.food_type_id AS food_type_id
                                FROM recipe
                                    LEFT JOIN author
                                        ON recipe.author_id =  author.author_id
                                    LEFT JOIN food_type
                                        ON recipe.food_type_id = food_type.food_type_id
                                    WHERE recipe.recipe_id = $recipe_id;";

        $results_recipe_details = $mysqli->query($sql_recipe_details);

        if (!$results_recipe_details) {
            echo $mysqli->error;
			$mysqli->close();
			exit();
        }

        $row = $results_recipe_details->fetch_assoc();

        // Convert ingredients
        $ingredients_array = explode(' | ', $row['ingredients']);
        // var_dump($ingredients_array);
        // foreach ($ingredients_array as $ingredient) {
        //     if (trim($ingredient) != "" && trim($ingredient) != "|") {
        //         echo "$ingredient <br>";
        //     }
        // }

        // Convert methods
        $methods_array = explode(' | ', $row['method']);
        // var_dump($methods_array);
        // foreach ($methods_array as $method) {
        //     if (trim($method) != "" && trim($method) != "|") {
        //         echo "$method <br>";
        //     }
        // }

        $results_food_type = $mysqli->query("SELECT * FROM food_type;");

        if ( !$results_food_type ) {
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }

        $results_author = $mysqli->query("SELECT * FROM author;");

        if ( !$results_author ) {
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }

        $mysqli->close();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Edit Recipe Form | Christmas Recipes</title>
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

        <div id="container<?php if (!isset($error)) echo '-long';?>">
            <div id="center-box">
                <div class="title">
                    <?php if (isset($error)) : ?>
                        <?php echo $error;?>
                    <?php else : ?>
                        Edit this recipe (*=Required)
                    <?php endif; ?>
                </div>
                <?php if (!isset($error)) : ?>
                    <br>
                    <form id="add-form" action="edit_confirmation.php" method="POST">
                        <input type="hidden" name="recipe_id" value="<?php echo $row['recipe_id'];?>">

                        <input type="hidden" id="ingredientsCount" name="ingredientsCount" value="">
                        <input type="hidden" id="methodsCount" name="methodsCount" value="">

                        <label for="recipe_name">*Enter the name of your recipe:</label>
                        <input type="text" id="recipe_name" name="recipe_name" placeholder="e.g. cookies, pies, etc." value="<?php echo $row['recipe_name'];?>">

                        <div id="author-change">
                            <label for="author_id">*Select an author for your recipe (or, select "other" to enter your own name):</label>
                            <select id="author_id" name="author_id">
                                <?php while ($row_author = $results_author->fetch_assoc()) : ?>
                                    <option value="<?php echo $row_author['author_id'];?>"
                                    <?php if ($row['author_id'] == $row_author['author_id']) : ?>
                                        selected
                                    <?php endif; ?>
                                    ><?php echo $row_author['author_name'];?></option>
                                <?php endwhile; ?>
                                <option value="0">Other</option>
                            </select> 
                        </div>
                        
                        <label for="food_type_id">*Select a food type for your recipe:</label>
                        <select id="food_type_id" name="food_type_id">
                            <?php while ( $row_food_type = $results_food_type->fetch_assoc() ) : ?>
                                <option value="<?php echo $row_food_type['food_type_id']; ?>" 
                                    <?php if ($row_food_type['food_type_id'] == $row['food_type_id']) : ?>
                                        selected
                                    <?php endif; ?>
                                >
                                    <?php echo $row_food_type['food_type_name']; ?>
                                </option>
                            <?php endwhile; ?>
                            <!-- <option value="meal" selected>Meal</option>
                            <option value="snack">Snack</option>
                            <option value="dessert">Dessert</option>
                            <option value="condiment">Condiment</option>
                            <option value="drink">Drink</option>
                            <option value="alcohol">Alcohol</option> -->
                        </select>   

                        <label for="recipe_url">Enter a dedicated article URL for your recipe: (max 200 char)</label>
                        <input type="text" id="recipe_url" name="recipe_url" placeholder="e.g. bbcgoodfood.com, etc." value="<?php echo $row['url'];?>">

                        <label for="image_url">Enter an image URL for your recipe: (max 200 char)</label>
                        <input type="text" id="image_url" name="image_url" placeholder="e.g. images.google.com, etc." value="<?php echo $row['image_url'];?>">

                        <label for="description">Enter a description about your recipe: (max 1000 char)</label>
                        <input type="text" id="description" name="description" placeholder="e.g. This recipe is perfect for..." value="<?php echo $row['description'];?>">

                        <hr> 
                        <div id="center-flex-box">
                            <div id="add-ingredients">
                                Add ingredients below:<br>

                                <div id="ingredient-boxes">
                                    <!-- <label for="ingredient_1">Ingredient 1</label><input type="text" id="ingredient_1" name="ingredient_1" placeholder="e.g. 1/2 tbsp of sugar, etc."><br> -->
                                    <?php $count = 1; ?>

                                    <?php
                                    // ingredientsCount++;
                                    // let label = document.createElement("label");
                                    // label.for = "ingredient_" + ingredientsCount;
                                    // label.innerHTML = "Ingredient " + ingredientsCount;
                                    // let input = document.createElement("input");
                                    // input.type = "text";
                                    // input.id = "ingredient_" + ingredientsCount;
                                    // input.name = "ingredient_" + ingredientsCount;
                                    // input.placeholder = "e.g. 1/2 tbsp of sugar, etc."
                                    // let br = document.createElement("br");
                                    ?>

                                    <?php foreach ($ingredients_array as $ingredient) : ?>
                                        <?php if (trim($ingredient) != '' && trim($ingredient) != '|') :?>
                                            <label for="ingredient_<?php echo $count;?>">Ingredient <?php echo $count;?></label><input type="text" id="ingredient_<?php echo $count;?>" name="ingredient_<?php echo $count;?>" placeholder="e.g. 1/2 tbsp of sugar, etc." value="<?php echo $ingredient; ?>"><br>
                                            <?php $count = $count + 1; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div> <!-- #ingredient-boxes -->

                                <input class="add-button" id="add-ingredient" type="button" value="Add another ingredient">
                                <input class="add-button" id="remove-ingredient" type="button" value="Remove last ingredient">
                            </div> <!-- #add-ingredients -->

                            <div id="add-methods">
                                Add methods below: <br>

                                <div id="method-boxes">
                                    <?php $methodCount = 1; ?>
                                    <?php foreach ($methods_array as $method) : ?>
                                        <?php if (trim($method) != '' && trim($method) != '|') :?>
                                            <label for="method_<?php echo $methodCount;?>">Method <?php echo $methodCount;?></label><input type="text" id="method_<?php echo $methodCount;?>" name="method_<?php echo $methodCount;?>" placeholder="e.g. Preheat oven to 300F, etc." value="<?php echo $method; ?>"><br>
                                            <?php $methodCount = $methodCount + 1; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div> <!-- #method-boxes -->

                                <input class="add-button" id="add-method" type="button" value="Add another method   ">
                                <input class="add-button" id="remove-method" type="button" value="Remove last method   ">
                            </div> <!-- #add-methods -->
                        </div> <!-- #center-flex-box -->

                        <input id="submit-btn" type="submit" value="Edit it!">
                    </form> <!-- #add-form-->
                <?php endif; ?>
            </div> <!-- #center-box -->
        </div> <!-- #container -->

        <div id="footer">
            Website created by Denis Mac.
        </div> <!-- #footer -->

        <script>
            let ingredientsCount = <?php echo $count; ?>;
            document.querySelector("#ingredientsCount").value = ingredientsCount;
            let methodsCount = <?php echo $methodCount; ?>;
            document.querySelector("#methodsCount").value = methodsCount;

            // createIngredientInput();
            // createMethodInput();

            function createIngredientInput() {
                let label = document.createElement("label");
                label.for = "ingredient_" + ingredientsCount;
                label.innerHTML = "Ingredient " + ingredientsCount;
                let input = document.createElement("input");
                input.type = "text";
                input.id = "ingredient_" + ingredientsCount;
                input.name = "ingredient_" + ingredientsCount;
                input.placeholder = "e.g. 1/2 tbsp of sugar, etc."
                let br = document.createElement("br");
                document.querySelector("#ingredient-boxes").appendChild(label);
                document.querySelector("#ingredient-boxes").appendChild(input);
                document.querySelector("#ingredient-boxes").appendChild(br)
                document.querySelector("#ingredient-boxes").appendChild(document.createElement("emptyTextNode"));
                ingredientsCount++;
                document.querySelector("#ingredientsCount").value = ingredientsCount;
            }

            function createMethodInput() {
                let label = document.createElement("label");
                label.for = "method_" + methodsCount;
                label.innerHTML = "Method " + methodsCount;
                let input = document.createElement("input");
                input.type = "text";
                input.id = "method_" + methodsCount;
                input.name = "method_" + methodsCount;
                input.placeholder = "e.g. Preheat oven to 300F, etc."
                let br = document.createElement("br");
                document.querySelector("#method-boxes").appendChild(label);
                document.querySelector("#method-boxes").appendChild(input);
                document.querySelector("#method-boxes").appendChild(br);
                document.querySelector("#method-boxes").appendChild(document.createElement("emptyTextNode"));
                methodsCount++;
                document.querySelector("#methodsCount").value = methodsCount;
            }

            // <label for="author_name">*Select an author for your recipe (or, select "other" to enter your own name):</label>
            //                 <select id="author_name" name="author_name">
            //                     <option value="" selected>Person 1</option>
            //                     <option value="">Person 2</option>
            //                     <option value="meal">Other</option>
            //                 </select> 

            // let label = document.createElement("label");
            // label.for = "author_name";
            // label.innerHTML = "*Select an author for your recipe (or, select 'other' to enter your own name):";
            // document.querySelector("#author-change").appendChild(label);

            // const authors = [];
            // <?php while ($row_author = $results_author->fetch_assoc()) : ?>
            //     var dict = {};
            //     dict['author_id'] = <?php echo json_encode($row_author['author_id']);?>;
            //     dict['author_name'] = <?php echo json_encode($row_author['author_name']);?>;
            //     authors.push(dict);
            // <?php endwhile; ?>
            // // console.log(authors);

            // function createAuthorSelect() {
            //     let select = document.createElement("select");
            //     select.id = "author_name";
            //     select.name = "author_name";
            //     for (author of authors) {
            //         // console.log(author)
            //         let option = document.createElement("option");
            //         option.value = author['author_id'];
            //         option.innerHTML = author['author_name'];
            //         select.appendChild(option);
            //     }
            //     let option = document.createElement("option");
            //     option.value = authors.length;
            //     option.innerHTML = "Other";
            //     select.appendChild(option)
            //     document.querySelector("#author-change").appendChild(select);
            // }

            // createAuthorSelect();

            // var totalAuthors = <?php echo $results_author->num_rows;?>;
            var editBox = false;

            document.querySelector("#author_id").onchange = () => {
                if (document.querySelector("#author_id").value == 0) {
                    let textBox = document.createElement("input");
                    textBox.type = "text";
                    textBox.id = "author_name";
                    textBox.name = "author_name";
                    textBox.placeholder = "e.g. John Doe, Tommy Trojan, etc...";
                    document.querySelector("#author-change").appendChild(textBox);
                    editBox = true;
                } else if (editBox) {
                    let list = document.querySelector("#author-change");
                    list.removeChild(list.lastChild);
                    editBox = false;
                }
            }

            document.querySelector("#add-ingredient").onclick = () => {
                createIngredientInput();
            }

            document.querySelector("#add-method").onclick = () => {
                createMethodInput();
            }

            document.querySelector("#remove-ingredient").onclick = () => {
                // console.log("ingredientsCount: " + ingredientsCount);
                // let num = 4;
                if (ingredientsCount == 0) {
                    return;
                }
                let list = document.querySelector("#ingredient-boxes");
                for (let i = 0; i < 4; i++) {
                    list.removeChild(list.lastChild);
                    console.log(list.lastChild);
                }
                ingredientsCount--;
            }

            document.querySelector("#remove-method").onclick = () => {
                // console.log("methodsCount: " + methodsCount);
                // let num = 3;
                if (methodsCount == 0) {
                    return;
                }
                let list = document.querySelector("#method-boxes");
                for (let i = 0; i < 4; i++) {
                    list.removeChild(list.lastChild);
                    // console.log(list.lastChild);
                }
                methodsCount--;
            }
        </script>
    </body>
</html>