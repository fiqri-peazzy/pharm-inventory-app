erDiagram
%% ========================================
%% MASTER DATA
%% ========================================

    users ||--o{ activity_logs : creates
    users ||--o{ receivings : creates
    users ||--o{ distributions : creates
    users ||--o{ prescriptions : creates
    users }o--|| warehouses : "assigned_to"
    users }o--|| roles : has

    roles ||--o{ role_has_permissions : has
    permissions ||--o{ role_has_permissions : belongs_to
    roles ||--o{ model_has_roles : assigned_to
    permissions ||--o{ model_has_permissions : assigned_to

    items ||--o{ item_batches : has
    items ||--o{ stock_cards : tracks
    items ||--o{ item_conversions : has
    items ||--o{ item_prices : has
    items }o--|| item_categories : belongs_to
    items }o--|| item_units : uses

    item_categories ||--o{ item_categories : "has_subcategory"

    item_conversions }o--|| item_units : from_unit
    item_conversions }o--|| item_units : to_unit

    warehouses ||--o{ item_batches : stores
    warehouses ||--o{ stock_cards : tracks
    warehouses ||--o{ stock_alerts : monitors

    suppliers ||--o{ item_prices : offers
    suppliers ||--o{ purchase_orders : receives
    suppliers ||--o{ receivings : supplies
    suppliers ||--o{ returns : returns_to

    %% ========================================
    %% PENGADAAN
    %% ========================================

    purchase_requests ||--o{ purchase_request_details : contains
    purchase_requests }o--|| warehouses : for
    purchase_requests ||--o{ purchase_orders : generates

    purchase_request_details }o--|| items : requests

    purchase_orders ||--o{ purchase_order_details : contains
    purchase_orders }o--|| suppliers : ordered_from
    purchase_orders }o--|| warehouses : for
    purchase_orders ||--o{ receivings : received_as

    purchase_order_details }o--|| items : orders

    receivings ||--o{ receiving_details : contains
    receivings }o--|| suppliers : from
    receivings }o--|| warehouses : to
    receivings ||--o{ item_batches : creates

    receiving_details }o--|| items : receives
    receiving_details ||--o{ item_batches : creates_batch

    %% ========================================
    %% STOK MANAGEMENT
    %% ========================================

    item_batches }o--|| items : of
    item_batches }o--|| warehouses : stored_in
    item_batches }o--|| suppliers : from
    item_batches ||--o{ stock_cards : tracked_by
    item_batches ||--o{ stock_alerts : monitors

    stock_cards }o--|| items : tracks
    stock_cards }o--|| warehouses : in
    stock_cards }o--|| item_batches : of_batch

    stock_alerts }o--|| items : alerts_for
    stock_alerts }o--|| warehouses : in
    stock_alerts }o--|| item_batches : of_batch

    %% ========================================
    %% DISTRIBUSI & PEMAKAIAN
    %% ========================================

    distributions ||--o{ distribution_details : contains
    distributions }o--|| warehouses : from
    distributions }o--|| warehouses : to

    distribution_details }o--|| items : distributes
    distribution_details }o--|| item_batches : uses_batch

    prescriptions ||--o{ prescription_details : contains
    prescriptions }o--|| warehouses : from

    prescription_details }o--|| items : dispenses
    prescription_details }o--|| item_batches : uses_batch

    %% ========================================
    %% AUDIT & KONTROL
    %% ========================================

    stock_opnames ||--o{ stock_opname_details : contains
    stock_opnames }o--|| warehouses : in

    stock_opname_details }o--|| items : counts
    stock_opname_details }o--|| item_batches : of_batch

    stock_adjustments ||--o{ stock_adjustment_details : contains
    stock_adjustments }o--|| warehouses : in

    stock_adjustment_details }o--|| items : adjusts
    stock_adjustment_details }o--|| item_batches : of_batch

    returns ||--o{ return_details : contains
    returns }o--|| warehouses : from
    returns }o--|| suppliers : to_supplier
    returns }o--|| warehouses : to_warehouse

    return_details }o--|| items : returns
    return_details }o--|| item_batches : of_batch

    disposals ||--o{ disposal_details : contains
    disposals }o--|| warehouses : from

    disposal_details }o--|| items : disposes
    disposal_details }o--|| item_batches : of_batch

    %% ========================================
    %% AKUNTANSI
    %% ========================================

    coa ||--o{ coa : "has_subaccount"
    coa ||--o{ journal_entry_details : used_in

    journal_entries ||--o{ journal_entry_details : contains

    %% ========================================
    %% ENTITY DEFINITIONS
    %% ========================================

    users {
        bigint id PK
        string name
        string email UK
        string username UK
        string password
        string employee_id
        string phone
        bigint warehouse_id FK
        boolean is_active
        timestamp last_login_at
        timestamps created_updated
    }

    roles {
        bigint id PK
        string name UK
        string guard_name
        timestamps created_updated
    }

    permissions {
        bigint id PK
        string name UK
        string guard_name
        timestamps created_updated
    }

    items {
        bigint id PK
        string code UK
        string nie_number
        string barcode
        string name
        string generic_name
        bigint item_category_id FK
        string manufacturer
        bigint item_unit_id FK
        integer min_stock
        integer max_stock
        boolean is_prescription
        boolean is_consignment
        boolean is_active
        string storage_condition
        string fornas_status
        string fornas_code
        text notes
        timestamps created_updated
        soft_deletes
    }

    item_categories {
        bigint id PK
        string code UK
        string name
        string type
        bigint parent_id FK
        boolean is_active
        timestamps created_updated
    }

    item_units {
        bigint id PK
        string code UK
        string name
        boolean is_active
        timestamps created_updated
    }

    item_conversions {
        bigint id PK
        bigint item_id FK
        bigint from_unit_id FK
        bigint to_unit_id FK
        decimal conversion_factor
        boolean is_base_unit
        timestamps created_updated
    }

    item_prices {
        bigint id PK
        bigint item_id FK
        bigint supplier_id FK
        string price_type
        decimal price
        decimal ppn_percentage
        date effective_date
        date end_date
        boolean is_active
        timestamps created_updated
    }

    suppliers {
        bigint id PK
        string code UK
        string name
        string type
        text address
        string phone
        string email
        string contact_person
        string npwp
        string tax_status
        integer payment_term
        string bank_name
        string bank_account_number
        string bank_account_name
        boolean is_active
        timestamps created_updated
    }

    warehouses {
        bigint id PK
        string code UK
        string name
        string type
        boolean is_main
        boolean is_active
        string pic_name
        string pic_phone
        text address
        timestamps created_updated
    }

    purchase_requests {
        bigint id PK
        string request_number UK
        bigint warehouse_id FK
        date request_date
        integer period_month
        integer period_year
        string status
        text notes
        timestamps submitted_approved_rejected
        timestamps created_updated
        soft_deletes
    }

    purchase_request_details {
        bigint id PK
        bigint purchase_request_id FK
        bigint item_id FK
        integer current_stock
        decimal average_usage
        integer requested_qty
        integer approved_qty
        text notes
        timestamps created_updated
    }

    purchase_orders {
        bigint id PK
        string po_number UK
        bigint purchase_request_id FK
        bigint supplier_id FK
        bigint warehouse_id FK
        date po_date
        date expected_delivery_date
        integer payment_term
        decimal total_amount
        decimal ppn_amount
        decimal discount_amount
        decimal grand_total
        string status
        text notes
        timestamps approved
        timestamps created_updated
        soft_deletes
    }

    purchase_order_details {
        bigint id PK
        bigint purchase_order_id FK
        bigint item_id FK
        integer qty_ordered
        integer qty_received
        decimal purchase_price
        decimal discount_percentage
        decimal discount_amount
        decimal ppn_percentage
        decimal ppn_amount
        decimal subtotal
        text notes
        timestamps created_updated
    }

    receivings {
        bigint id PK
        string receiving_number UK
        bigint purchase_order_id FK
        bigint supplier_id FK
        bigint warehouse_id FK
        date receiving_date
        string invoice_number
        date invoice_date
        decimal total_amount
        decimal ppn_amount
        decimal grand_total
        text notes
        string status
        timestamps approved
        timestamps created_updated
        soft_deletes
    }

    receiving_details {
        bigint id PK
        bigint receiving_id FK
        bigint item_id FK
        string batch_number
        date expired_date
        integer qty_received
        decimal purchase_price
        decimal ppn_percentage
        decimal ppn_amount
        decimal subtotal
        timestamps created_updated
    }

    item_batches {
        bigint id PK
        bigint item_id FK
        bigint warehouse_id FK
        string batch_number
        date expired_date
        date manufactured_date
        decimal purchase_price
        decimal ppn_percentage
        decimal ppn_amount
        decimal discount_percentage
        decimal discount_amount
        decimal total_price
        decimal selling_price
        decimal margin_percentage
        bigint supplier_id FK
        bigint receiving_id FK
        integer initial_stock
        integer current_stock
        boolean is_expired
        boolean is_active
        timestamps created_updated
        soft_deletes
    }

    stock_cards {
        bigint id PK
        bigint item_id FK
        bigint warehouse_id FK
        bigint item_batch_id FK
        string transaction_type
        bigint transaction_id
        string reference_number
        date transaction_date
        integer stock_before
        integer stock_in
        integer stock_out
        integer stock_after
        decimal price_per_unit
        decimal total_value
        text notes
        timestamps created_updated
    }

    stock_alerts {
        bigint id PK
        string alert_type
        bigint item_id FK
        bigint warehouse_id FK
        bigint item_batch_id FK
        integer current_stock
        integer threshold_value
        date expired_date
        integer days_to_expired
        timestamp alert_date
        boolean is_read
        timestamps read
        text notes
        timestamps created_updated
    }

    distributions {
        bigint id PK
        string distribution_number UK
        bigint from_warehouse_id FK
        bigint to_warehouse_id FK
        date distribution_date
        text notes
        string status
        timestamps approved
        timestamps created_updated
        soft_deletes
    }

    distribution_details {
        bigint id PK
        bigint distribution_id FK
        bigint item_id FK
        bigint item_batch_id FK
        integer qty
        decimal price_per_unit
        decimal subtotal
        timestamps created_updated
    }

    prescriptions {
        bigint id PK
        string prescription_number UK
        string patient_id
        string patient_name
        string medical_record_number
        string doctor_id
        string doctor_name
        date prescription_date
        bigint warehouse_id FK
        decimal total_amount
        string status
        timestamps processed
        timestamps created_updated
        soft_deletes
    }

    prescription_details {
        bigint id PK
        bigint prescription_id FK
        bigint item_id FK
        bigint item_batch_id FK
        integer qty
        decimal price_per_unit
        decimal subtotal
        text instruction
        timestamps created_updated
    }

    stock_opnames {
        bigint id PK
        string opname_number UK
        bigint warehouse_id FK
        date opname_date
        string status
        decimal total_difference_value
        timestamps approved
        timestamps created_updated
        soft_deletes
    }

    stock_opname_details {
        bigint id PK
        bigint stock_opname_id FK
        bigint item_id FK
        bigint item_batch_id FK
        integer system_stock
        integer physical_stock
        integer difference
        decimal price_per_unit
        decimal difference_value
        text notes
        timestamps created_updated
    }

    stock_adjustments {
        bigint id PK
        string adjustment_number UK
        bigint warehouse_id FK
        date adjustment_date
        string adjustment_type
        decimal total_value
        string reason
        string berita_acara_number
        text notes
        string status
        timestamps approved
        timestamps created_updated
        soft_deletes
    }

    stock_adjustment_details {
        bigint id PK
        bigint stock_adjustment_id FK
        bigint item_id FK
        bigint item_batch_id FK
        integer system_stock
        integer adjusted_stock
        integer difference
        decimal price_per_unit
        decimal total_value
        text reason
        timestamps created_updated
    }

    returns {
        bigint id PK
        string return_number UK
        string return_type
        bigint warehouse_id FK
        bigint supplier_id FK
        bigint from_warehouse_id FK
        date return_date
        string invoice_number
        decimal total_amount
        text reason
        string status
        timestamps approved
        timestamps created_updated
        soft_deletes
    }

    return_details {
        bigint id PK
        bigint return_id FK
        bigint item_id FK
        bigint item_batch_id FK
        integer qty
        decimal price_per_unit
        decimal subtotal
        text reason
        timestamps created_updated
    }

    disposals {
        bigint id PK
        string disposal_number UK
        bigint warehouse_id FK
        date disposal_date
        string disposal_type
        decimal total_value
        string berita_acara_number
        json witnesses
        text notes
        string status
        timestamps approved
        timestamps created_updated
        soft_deletes
    }

    disposal_details {
        bigint id PK
        bigint disposal_id FK
        bigint item_id FK
        bigint item_batch_id FK
        integer qty
        decimal price_per_unit
        decimal total_value
        text reason
        timestamps created_updated
    }

    coa {
        bigint id PK
        string account_code UK
        string account_name
        string account_type
        bigint parent_id FK
        boolean is_active
        timestamps created_updated
    }

    journal_entries {
        bigint id PK
        string journal_number UK
        string transaction_type
        bigint transaction_id
        date transaction_date
        text description
        decimal total_debit
        decimal total_credit
        string status
        timestamps posted
        timestamps created_updated
        soft_deletes
    }

    journal_entry_details {
        bigint id PK
        bigint journal_entry_id FK
        bigint coa_id FK
        decimal debit
        decimal credit
        text description
        timestamps created_updated
    }

    activity_logs {
        bigint id PK
        bigint user_id FK
        string action
        string module
        bigint record_id
        json old_values
        json new_values
        string ip_address
        string user_agent
        timestamps created_updated
    }
