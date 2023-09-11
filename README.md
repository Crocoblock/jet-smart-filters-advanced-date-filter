## How to use the plugin

1. Create `Date range` filter
2. Select `Meta Date` in the `Filter by` option
3. Set this into `Query Variable` option - advanced_date__each__field-1__field-2

Where:
- `advanced_date__` - is the reserved word, you always need to use it.
- Parameter which defines how to filter the data. Allowed values:
  - `any` - if any field inside the range, 
  - `inside` - it adds the item to query results if any of requested field inside the range, or if whole range between this 2 fields, works only if you set filter by 2 dates,
  - `each`, `fields_inside` - it adds the item to query results if both post fields are inside the search range, 
  - `range_inside` - adds the item to query results if whole search range is between post fields;
- `field-1__field-2` - double underscore-separated list of fields to search by. `any` and `each` types supports any number of passed fields, `inside` type supports only 2 fields.

Examples:

variable: advanced_date__each__start_date__end_date
existing dates: June 2 - June 7, June 9 - June 15, June 5 - June 12, June 1 - June 11
requested range: June 1 - June 9
matched dates: June 2 - June 7

variable: advanced_date__any__start_date__end_date
existing dates: June 2 - June 7, June 9 - June 15, June 5 - June 12, June 1 - June 11
requested range: June 2 - June 9
matched dates: June 2 - June 7, June 9 - June 15, June 5 - June 12

variable: advanced_date__inside__start_date__end_date
existing dates: June 2 - June 7, June 9 - June 15, June 5 - June 12, June 1 - June 11
requested range: June 3 - June 8
matched dates: June 2 - June 7, June 5 - June 12, June 1 - June 11


## How to use the plugin (legacy instructions, work with AJAX filters mode only)

1. Create `Date range` filter
2. Select `Meta Date` in the `Filter by` option
3. Set this into `Query Variable` option - advanced_date::each::field-1;field-2.

Where:
- `advanced_date` - is the reserved word, you always need to use it.
- Parameter which defines how to filter the data. Allowed values:
  - `any` - if any field inside the range, 
  - `inside` - it adds the item to query results if any of requested field inside the range, or if whole range between this 2 fields, works only if you set filter by 2 dates,
  - `each`, `fields_inside` - it adds the item to query results if both post fields are inside the search range, 
  - `range_inside` - adds the item to query results if whole search range is between post fields;
- `field-1;field-2` - semicolon-separated list of fields to search by. `any` and `each` types supports any number of passed fields, `inside` type supports only 2 fields.

Examples:

variable: advanced_date::each::start_date;end_date
existing dates: June 2 - June 7, June 9 - June 15, June 5 - June 12, June 1 - June 11
requested range: June 1 - June 9
matched dates: June 2 - June 7

variable: advanced_date::any::start_date;end_date
existing dates: June 2 - June 7, June 9 - June 15, June 5 - June 12, June 1 - June 11
requested range: June 2 - June 9
matched dates: June 2 - June 7, June 9 - June 15, June 5 - June 12

variable: advanced_date::inside::start_date;end_date
existing dates: June 2 - June 7, June 9 - June 15, June 5 - June 12, June 1 - June 11
requested range: June 3 - June 8
matched dates: June 2 - June 7, June 5 - June 12, June 1 - June 11
