# Domus Customer Delivery Pincode Checker

## Premium Magento 2 Pincode Delivery Checker Module

A professional, high-performance delivery validation engine for Magento 2. Supports product-level serviceability checks, seamless checkout integration, and multi-provider logistics support (Shiprocket, Manual).

---

## ✅ Core Features

### 🚀 "Perfect Sync" Checkout Integration
Unlike standard modules that add redundant fields, this module **hooks directly into the native Magento 2 postcode field**.
- **Real-time Validation**: As the user types their address, the module validates deliverability in the background.
- **Inline Messaging**: Success/Failure messages and ETAs appear directly beneath the standard field.
- **Checkout Blocking**: Prevents order placement if the entered pincode is non-deliverable.

### 📦 Product-Specific Information
Displays critical product data alongside delivery results to increase conversion:
- **Warranty Display**: "Original manufacturer's warranty" information pulled directly from product attributes.
- **Returnable Status**: Shows whether the item is returnable (e.g., "Non-returnable" or "7-day easy return").

### 🔌 Extensible Logistics Architecture
The module features a **Provider-based Architecture**, allowing you to switch logistics partners instantly:
- **Manual/Internal Mode**: Run your own delivery network using local database rules with zero API dependencies.
- **Shiprocket Integration**: Connect your Shiprocket account for live courier serviceability and real-time ETAs.
- **Future-Proof**: Easily add new partners (Delhivery, BlueDart, etc.) by creating a single provider class.

### 🛠️ Advanced Admin Controls
- **Pincode CRUD**: Full management grid with mass actions and inline editing.
- **Import/Export**: Bulk upload pincodes, shipping charges, and COD rules via CSV.
- **Rule Hierarchy**: Priority-based matching for complex delivery scenarios.
- **Restrictions**: Define rules based on Categories, Products, Weight, Price, Store, or Customer Group.

---

## 🔧 Installation

### Manual Installation
1. Create directory `app/code/Domus/CustomerDeliveryChecker`
2. Copy all module files into the directory.
3. Run the following commands:
```bash
bin/magento module:enable Domus_CustomerDeliveryChecker
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

---

## ⚙️ Configuration

Navigate to: **Stores > Configuration > Domus > Delivery Pincode Checker**

### General Settings
- **Logistics Provider**: Toggle between **Manual/Internal** or **Shiprocket**.
- **Show on Product Page**: Enable/Disable the AJAX checker on product detail pages.
- **Show Warranty Information**: Toggle the display of warranty/returnable rows.

### Shiprocket Settings
- **Shiprocket API Key**: Enter your bearer token.
- **Pickup Pincode**: Set your warehouse location for ETA calculations.

---

## 📊 Importing Pincodes

### CSV Format
Prepare a CSV with the following headers:
`pincode,country_id,city,state,area_name,is_deliverable,is_cod_available,estimated_delivery_days,shipping_charge`

### CLI Import
```bash
bin/magento domus:pincode:import /path/to/pincodes.csv
```

---

## ⚙️ Magento 2.4.8+ & PHP 8.3+ Compatibility
✅ **Fully Compatible**
- Optimized for Gemmart Theme and standard Luma-based themes.
- Complies with `strict_types=1` and modern PHP 8 standards.

---

## 📝 License
Proprietary - Domus Extensions.

---

## 🆘 Support
For technical support or feature requests, contact:
- All rights reserved.

---

## 🆘 Support

For support, please contact:
