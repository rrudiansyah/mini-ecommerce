UPDATE products p
INNER JOIN (
    SELECT DISTINCT product_id FROM product_recipes
) r ON p.id = r.product_id
SET p.hpp_type = 'auto'
WHERE p.store_id = 1
  AND p.hpp_type != 'auto';