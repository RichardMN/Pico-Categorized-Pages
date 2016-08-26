# Pico-Categorized-Pages
A little [Pico-CMS](http://picocms.org/) plugin allowing you to automatically sort (ascending or descending), position and categorize your pages from your content folder structure.

## Features

- PICO 1.0.0 compatible
- Category and pages sorting (asc or desc)
- Category and pages ignore
- Multilingual categories with language-specific titles

## General Purpose

The purpose here is to be able to generate a navigation menu simply by writing the script below.
```twig
    <nav>
      <ul>
      {% for category in categories %}
        <li>
          <a href="{{ category.pages[1].url }}">{{ category.title }}</a>
          {% if category.pages|length > 1 %}
          <ul>
          {% for page in category.pages %}
            <li class="sub"><a href="{{ page.url }}">{{ page.title }}</a></li>
          {% endfor %}
          </ul>
          {% endif %}
        </li>
      {% endfor %}
      </ul>
    </nav>
```
## How-to

First, be sure to configure it properly
```php
    $config['pages_order_by'] = 'position';             // Needed by PicoCategorizedPages
    $config['pages_order'] = 'asc';                 // Order pages "asc" or "desc"
    $config['categories_order'] = 'asc';              // Order categories "asc" or "desc"
    
    $config['PicoCategorizedPages.enabled'] = true;    // Force PicoCategorizedPages to be enabled
```

A category is simply a folder inside your content folder descripted by the index.md of that folder.
The index.md is where the position in the menu and its title will be set.
The same logic applies to each file inside that folder, except they won't have any impact on the folder declaration.

Category tags (index.md in a folder) :

    ---
    Title: Title of the page
    Position: Position of the page
    Page_Ignore: Set to true or false, will ignore this page in the generated array
    Category_Title: Title of the category
    Category_Position: Position of the category
    Category_Ignore: Set to true or false, will ignore this category in the generated array
    ---
    
Page tags (anything.md in a folder) :

    ---
    Title: Title of the page
    Position: Position of the page
    Page_Ignore: Set to true or false, will ignore this page in the generated array
    ---

## Example

Let's consider the following content structure :

    ./content
      ./Bees
        ./index.md
        ./hive.md
        ./honey.md
      ./Ants
        ./index.md
        ./queen.md
        ./solders.md
        
Index.md for Bees :

    ---
    Title: Welcome to the bees !
    Position: 1
    Category_Position: 2
    Category_Title: Beez ?!
    ---
    
Will create a category at the second position (Category_Position) with the title "Beez ?!" (Category_Title) and the page itself will be titled "Welcome to the bees" and will be te first item in the list.

The same applies for hive.md :

    ---
    Title: Hive is good
    Position: 3
    ---

and honey.md

    ---
    Title: The sweet honey boogie
    Position: 2
    ---
    
Will output :
```html
    <nav>
      <ul>
        ...
        <li>
          <a href="yoururl../Bees/">Beez ?!</a>
          <ul>
            <li class="sub"><a href="yoururl../Bees/">Welcome to the bees !</a></li>
            <li class="sub"><a href="yoururl../Bees/honey">The sweet honey boogie</a></li>
            <li class="sub"><a href="yoururl../Bees/hive">Hive is good</a></li>
          </ul>
        </li>
      </ul>
    </nav>
```

## Multilingual support

Used in conjunction with the
[Pico Multilingual plugin](https://github.com/RichardMN/pico_multilanguage),
this plugin can also provide specific category titles by language.

The category tags in the index.md file include a new array of titles, one for
each language, using the same language codes used for the Multilingual
plugin.
    ---
    Title: Title of the page
    Position: Position of the page
    Page_Ignore: Set to true or false, will ignore this page in the generated array
    Category_Title: Title of the category
    Category_Titles:
	  en: English Title of the Category
	  fr: Titre en français
    Category_Position: Position of the category
    Category_Ignore: Set to true or false, will ignore this category in the generated array
    ---

Then based on the language of each page shown, the appropriate language
title for the category will be provided. The twig code should not need changes (though
you may need to rewrite your twig code to take advantage of other elements from
the Multilingual plugin.

Note that there is still no provision for index.md to have more than one language
in its content.

## Installation
Simply put PicoCategorizedPages.php in your plugins folder and it will be fine as it is.
But don't forget to set your config.php right as mentioned in the How-To.

## What's next ?

- Recursive categories
