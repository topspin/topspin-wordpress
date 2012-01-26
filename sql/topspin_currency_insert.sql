INSERT INTO `<?php echo $wpdb->prefix;?>topspin_currency` (`currency`, `symbol`) VALUES
('USD', '$'),
('GBP', '&pound;'),
('CAD', '$') ON DUPLICATE KEY UPDATE `currency`=`currency`;