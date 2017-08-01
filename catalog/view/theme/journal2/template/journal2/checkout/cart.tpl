<!-- Fuente del table responsive -->
<!-- https://css-tricks.com/responsive-data-tables/ -->
  <?php if(isMobile()): ?>
<style type="text/css">
table { 
  width: 100%; 
  border-collapse: collapse; 
}
/* Zebra striping */
/*
tr:nth-of-type(odd) { 
  background: #eee; 
}
*/
th { 
  background: #333; 
  color: white; 
  font-weight: bold; 
}
td, th { 
  padding: 6px; 
  border: 1px solid #ccc; 
  text-align: left; 
}
@media 
only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px)  {

  table, thead, tbody, th, td, tr { 
    display: block; 
  }
  
  thead tr { 
    position: absolute;
    top: -9999px;
    left: -9999px;
  }
  
  tr { border: 1px solid #ccc; }
  
  td { 
    border: none;
    border-bottom: 1px solid #eee; 
    position: relative;
    padding-left: 50%; 
  }
  
  td:before { 
    position: absolute;
    top: 6px;
    left: 6px;
    width: 45%; 
    padding-right: 10px; 
    white-space: nowrap;
    font-weight:bold;
  }
  
  /*
  Label the data
  */
  td:before { content: attr(data-label); }
  table, thead, tbody, th, td, tr{
  display: block;
  width:100%;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  float:left;
  clear:left;
  }
}
</style>
<?php endif; ?>
<div class="checkout-content checkout-cart">
    <h2 class="secondary-title"><?php echo $this->journal2->settings->get('one_page_lang_shop_cart', 'Carrito de Compra'); ?></h2>
  <?php if(isMobile()): ?>
  <div class="checkout-product">
  <table>
    <?php  else: ?>
    <div class="table-responsive checkout-product">
        <table class="table table-bordered table-hover">
    <?php  endif; ?>

            <thead>
            <tr>
                <td class="text-left name" colspan="2"><?php echo $column_name; ?></td>
                <td class="text-left quantity"><?php echo $column_quantity; ?></td>
                <?php if(!isMobile()): ?>
                <td class="text-right price"><?php echo $column_price; ?></td>
                <?php endif; ?>
                <td class="text-right total"><?php echo $column_total; ?></td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product) { ?>
                <tr>
                    <td class="text-center image"><?php if ($product['thumb']) { ?>
                            <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-thumbnail" /></a>
                        <?php } ?></td>
                    <td class="text-left name"><a
                            href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                        <?php foreach ($product['option'] as $option) { ?>
                            <br/>
                            &nbsp;
                            <small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
                        <?php } ?>
                        <?php if ($product['recurring']) { ?>
                            <br/>
                            <span class="label label-info"><?php echo $text_recurring_item; ?></span>
                            <small><?php echo $product['recurring']; ?></small>
                        <?php } ?></td>
                    <td class="text-left quantity">
                        <div class="input-group btn-block" style="max-width: 200px;">
                            <input type="text" name="quantity[<?php echo $product[version_compare(VERSION, '2.1', '<') ? 'key' : 'cart_id']; ?>]" value="<?php echo $product['quantity']; ?>" size="1" class="form-control" />
                            <span class="input-group-btn">
                                <button type="submit" data-toggle="tooltip" title="<?php echo $button_update; ?>" data-product-key="<?php echo $product[version_compare(VERSION, '2.1', '<') ? 'key' : 'cart_id']; ?>" class="btn btn-primary btn-update"><i class="fa fa-refresh"></i></button>
                                <button type="button" data-toggle="tooltip" title="<?php echo $button_remove; ?>" data-product-key="<?php echo $product[version_compare(VERSION, '2.1', '<') ? 'key' : 'cart_id']; ?>" class="btn btn-danger  btn-delete"><i class="fa fa-times-circle"></i></button>
                            </span>
                        </div>
                    </td>
                    <?php if(!isMobile()): ?>
                    <td class="text-right price"><?php echo $product['price']; ?></td>
                    <?php endif; ?>
                    <td class="text-right total"><?php echo $product['total']; ?></td>
                </tr>
            <?php } ?>
            <?php foreach ($vouchers as $voucher) { ?>
                <tr>
                    <td class="text-left"><?php echo $voucher['description']; ?></td>
                    <td class="text-left"></td>
                    <td class="text-right">1</td>
                    <td class="text-right"><?php echo $voucher['amount']; ?></td>
                    <td class="text-right"><?php echo $voucher['amount']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        <?php if(!isMobile()): ?>
            <tfoot>
        <?php endif; ?>            
            <?php foreach ($totals as $total) { ?>
                <tr>
                    <td colspan="4" class="text-right"><?php echo $total['title']; ?>:</td>
                    <td class="text-right"><?php echo $total['text']; ?></td>
                </tr>
            <?php } ?>
        <?php if(!isMobile()): ?>
            </tfoot>
        <?php endif; ?>            
        </table>
    </div>
    <div id="payment-confirm-button" class="payment-<?php echo Journal2Utils::getProperty($this->session->data, 'payment_method.code'); ?>">
        <h2 class="secondary-title"><?php echo $this->journal2->settings->get('one_page_lang_payment_details', 'Payment Details'); ?></h2>
        <?php echo $payment; ?>
    </div>
</div>

