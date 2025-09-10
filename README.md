
# UCM Related Terms

A simple WordPress plugin that provides:

- A function `ucm_get_related_terms($term_id, $taxonomy, $limit)` to get the most related terms from a different taxonomy.
- A widget to display related terms in the sidebar.
- A REST API endpoint for use with JavaScript/front-end frameworks.

## Function: `ucm_get_related_terms()`

### Parameters:

- `$term_id` (int): The term ID you want to find related terms for.
- `$taxonomy` (string): The taxonomy of the **related** terms.
- `$limit` (int): Number of related terms to return (default: 5).

### Example usage:

```php
$related = ucm_get_related_terms(23, 'category', 5);
foreach ($related as $term) {
    echo $term->name;
}
```

## REST API

### Endpoint:

```
GET /wp-json/ucm/v1/related-terms?term_id=23&taxonomy=category&limit=5
```

### Example Response:

```json
[
  {
    "id": 45,
    "name": "WordPress",
    "slug": "wordpress",
    "count": 8,
    "link": "https://example.com/tag/wordpress/"
  },
  ...
]
```

## Widget

Go to **Appearance → Widgets** to add the "UCM Related Terms" widget. It includes:

- Title field
- Taxonomy dropdown (Tag or Category)
- Limit field

## Installation

1. Upload the plugin folder to `/wp-content/plugins/ucm-related-terms/`
2. Activate via WP Admin → Plugins
3. Use the function, widget, or API as needed

---

Developed by ChatGPT 🚀
