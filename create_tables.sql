CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_name` varchar(255) NOT NULL,
  `niche` varchar(255) DEFAULT NULL,
  `base_url` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_numbers` text DEFAULT NULL,
  `business_description` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT 'callStack',
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `category` (`category`),
  CONSTRAINT `leads_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert a default user for testing
INSERT INTO `users` (`name`, `email`, `password`) VALUES
('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert some sample leads
INSERT INTO `leads` (`business_name`, `niche`, `email`, `phone_numbers`, `website`, `notes`, `category`) VALUES
('Digital Marketing Experts', 'Digital Marketing', 'contact@digitalmarketingexperts.com', '[\"555-123-4567\", \"555-765-4321\"]', 'digitalmarketingexperts.com', 'Specializes in SEO and PPC campaigns', 'callStack'),
('Web Solutions Inc.', 'Web Development', 'info@websolutions.com', '[\"555-234-5678\"]', 'websolutions.com', 'Looking for e-commerce development', 'coldLeads'),
('Creative Design Studio', 'Graphic Design', 'hello@creativedesign.com', '[\"555-345-6789\"]', 'creativedesign.com', 'Interested in brand refresh', 'warmLeads'),
('Content Writers Pro', 'Content Marketing', 'writers@contentpro.com', '[\"555-456-7890\"]', 'contentwriterspro.com', 'Current client for blog management', 'currentlyWorkingWith');
