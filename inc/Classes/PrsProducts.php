<?php

namespace PrioritySync;

class PrsProducts
{
    private $max_products;
    private $active_products;
    public $prs_products;

    public function __construct()
    {
        $products = $this->get_products_data();
        if ($products) {
            $this->prs_products = $products;
        } else {
            $this->prs_products = array();
        }
        $this->set_products_limit(100);

        //before use ajax add calss init to main plugin file!
        add_action('wp_ajax_prs_add_product', [$this, 'add_product']);
        add_action('wp_ajax_prs_search_for_product', [$this, 'search_for_product']);
        add_action('wp_ajax_prs_add_all_products', [$this, 'add_all_products']);
        add_action('wp_ajax_prs_update_all_products', [$this, 'update_all_products']);
    }

    public function get_products_data()
    {
        $file = PRS_DATA_FOLDER . 'sync/products.json';
        $subform = get_option('prs_priority_parts_expand');
        preg_match("/select=(\w*)/i", $subform, $selector);
        $selector = explode("=", $selector[0])[1];
        $subform_name = explode("(", $subform)[0];
        $products = array();
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file));
            if (isset($data->value)) {
                foreach ($data->value as $index => $part) {
                    $product = array();
                    foreach ($part as $key => $value) {
                        switch ($key) {
                            case 'PARTNAME':
                                $product['SKU'] = $value;
                                break;
                            case 'PARTDES':
                                $product['name'] = $value;
                                break;
                            case 'BASEPLPRICE':
                                $product['price'] = $value;
                                break;
                            case 'WSPLPRICE':
                                $product['wholesale_price'] = $value;
                                break;
                            case $subform_name:
                                $product['stock'] = isset($value[0]->$selector) ? $value[0]->$selector : 1;
                                break;
                            default:
                                # code...
                                break;
                        }
                    }
                    $products[$index] = $product;
                }
                return $products;
            }
        }
        return false;
    }

    public function get_products_limit()
    {
        return $this->max_products;
    }

    public function set_products_limit($limit = 500)
    {
        $this->max_products = $limit;
    }

    public function get_active_products()
    {
        return $this->active_products;
    }

    public function set_active_products($count)
    {
        $this->active_products = $count;
    }

    public function get_counted_products()
    {
        if (isset($this->counted_products))
            return $this->counted_products;
    }

    public function set_counted_products($count)
    {
        $this->count_products = $count;
    }

    public function view_products()
    {
        $all_sku = $this->get_existings_products_skus();
        $sku_not_exists =array();
        $count = 1;
        $active = 0;
        $table_data = "<table id='products_table' class='widefat striped fixed_head'><thead>" . $this->view_products_table_head() . "</thead>";
        $table_data .= "<tbody>";
        if (isset($this->prs_products)) {
            foreach ($this->prs_products as $product) {
                // if ($product["price"] == "0") continue;
                // if ($product["stock"] == "0") continue;
                if(!in_array($product['SKU'],$all_sku)){
                    $sku_not_exists[] = $product['SKU'];
                }
                $table_data .= $this->view_product_line($product);
                $active++;
                $count++;
                if ($count > $this->get_products_limit())
                    break;
            }
        }
        $table_data .= "</tbody>";
        $table_data .= "</table>";
        foreach ($sku_not_exists as $value) {
            $table_data .= "<div>$value</div>";
        }
        $this->set_active_products($active);
        $this->set_counted_products($count);
        return $table_data;
    }

    function search_for_product()
    {
        $serch_txt = $_POST['data'] . '';
        $table_data = '';
        $count = 0;
        if (strlen($serch_txt) > 2) {
            $table_data .= "<table id='search_table' class='widefat striped fixed_head'><thead>" . $this->view_products_table_head() . "</thead>";
            foreach ($this->prs_products as $product) {
                if ((stripos($product['name'], $serch_txt) !== false || stripos($product['SKU'], $serch_txt) !== false)) {
                    $table_data .= $this->view_product_line($product);
                    $count++;
                }
            }
            $table_data .= "</table>";
            if (!$count > 0) {
                echo '<h2>' . $serch_txt . ' not found</h2>';
                return;
            }
        } else {
            echo "search must be more than 3 signs!";
        }
        echo $table_data;
        die();
    }

    function view_products_table_head()
    {
        return "<tr>
                <th>SKU</th>
                <th>name</th>
                <th>price </th>
                <th>wholesale price</th>
                <th>stock</th>
                <th>add</th></tr>";
    }

    function view_product_line($product)
    {
        $product_line = "<tr class='product'>
        <td data-column='SKU'>{$product['SKU']}</td>
        <td data-column='name'>{$product['name']}</td>
        <td data-column='price'>" . PrsHelper::num_format($product['price'], 2) . "</td>
        <td data-column='wholesale_price'>" . PrsHelper::num_format($product['wholesale_price'], 2) . "</td>
        <td data-column='stock'>" . PrsHelper::num_format($product['stock']) . "</td>
        <td><button class='button action'>Add</button></td></tr>";
        return $product_line;
    }

    function get_new_product_array($product_name)
    {
        return array(
            'post_title' => $product_name,
            'post_content' => '',
            'post_status' => 'draft',
            'post_type' => 'product'
        );
    }

    function add_product($product_data = '')
    {
        // check_ajax_referer('prs', 'security');
        if ($product_data == '') {
            $product_data = $_POST['data'];
        }
        return $this->add_one_product($product_data);
    }

    function add_one_product($product_data)
    {
        $all_sku = $this->get_existings_products_skus();
        if (!in_array($product_data['SKU'], $all_sku)) {
            $product = $this->get_new_product_array($product_data['name']);
            $post_id = wp_insert_post($product);
            wp_set_object_terms($post_id, 'simple', 'product_type');
            update_post_meta($post_id, '_regular_price', intval($product_data['price'], 10));
            update_post_meta($post_id, '_price', intval($product_data['price'], 10));
            $whPrice = intval($product_data['wholesale_price'], 10);
            if ($whPrice > 0) {
                update_post_meta($post_id, 'wholesale_customer_wholesale_price', $whPrice);
                update_post_meta($post_id, 'wholesale_customer_have_wholesale_price', 'yes');
            } else {
                update_post_meta($post_id, 'wholesale_customer_wholesale_price', "");
                update_post_meta($post_id, 'wholesale_customer_have_wholesale_price', 'no');
            }
            update_post_meta($post_id, '_sku', $product_data['SKU']);
            update_post_meta($post_id, '_manage_stock', 'yes');
            update_post_meta($post_id, '_backorders', 'yes');
            update_post_meta($post_id, '_stock', intval($product_data['stock'], 10));
            echo ($product_data['SKU'] . " added successfuly!");
        } else {
            echo 'Product with SKU:' . $product_data['SKU'] . ' Exists!';
        }
        return true;
    }

    function add_all_products()
    {
        $count = 0;
        $products = array();
        if ($_POST['data']) {
            $products = $_POST['data'];
        } else {
            $products = $this->prs_products;
        }
        foreach ($products as $product) {
            // if ($product["price"] == "0") continue;
            // if ($product["stock"] == "0") continue;
            $this->add_one_product($product);
            $count++;
            if ($count > $this->get_products_limit())
                break;
        }
        echo (" - " . $count . ' new products added!');
    }

    function update_all_products()
    {
        global $wpdb;
        $count = 0;
        $success = 0;
        $all_sku = $this->get_existings_products_skus();
        foreach ($this->prs_products as $product) {
            if (in_array($product['SKU'], $all_sku)) {
                $product_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $product['SKU']));
                update_post_meta($product_id, '_regular_price', intval($product['price'], 10));
                update_post_meta($product_id, '_price', intval($product['price'], 10));
                $whPrice = intval($product['wholesale_price'], 10);
                if ($whPrice > 0) {
                    update_post_meta($product_id, 'wholesale_customer_wholesale_price', $whPrice);
                    update_post_meta($product_id, 'wholesale_customer_have_wholesale_price', 'yes');
                } else {
                    update_post_meta($product_id, 'wholesale_customer_wholesale_price', "");
                    update_post_meta($product_id, 'wholesale_customer_have_wholesale_price', 'no');
                }
                update_post_meta($product_id, '_stock', intval($product['stock'], 10));
                update_post_meta($product_id, '_stock_status', 'instock');
                $success++;
            }
            $count++;
        }
        $msg = "Total: $count, Updated: $success ";
        echo $msg;
        PrsLogger::log_message($msg);
    }

    function get_existings_products_skus()
    {
        $all_sku = array();
        $products_ids = get_posts(array(
            'numberposts' => -1,
            'post_type' => 'product',
            'post_status' => 'any',
            'fields' => 'ids',

        ));
        foreach ($products_ids as $product_id) {
            $product = wc_get_product($product_id);
            array_push($all_sku, $product->get_sku());
        }
        return $all_sku;
    }
}
