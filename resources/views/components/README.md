# Reusable Navigation System Documentation

This Laravel Blade component system provides a clean, maintainable way to create consistent navigation menus with automatic active state detection.

## Components Created

1. **`nav-item.blade.php`** - The main navigation component
2. **`NavigationHelper.php`** - Helper class for icons and route utilities
3. **Custom Blade Directives** - Simplified helper methods

## Features

- ✅ Automatic active state detection
- ✅ Support for dropdown menus
- ✅ Consistent styling and behavior
- ✅ Centralized icon management
- ✅ Route pattern matching
- ✅ Custom active classes
- ✅ Two sizes (normal and small)
- ✅ Reusable across different modules

## Usage Examples

### Basic Navigation Item

```blade
<x-nav-item 
    route="dashboard" 
    :icon="App\Helpers\NavigationHelper::getIcon('dashboard')" 
    title="Dashboard" 
/>
```

### Navigation Item with Route Pattern

```blade
<x-nav-item 
    route-pattern="inventory.*" 
    :icon="App\Helpers\NavigationHelper::getIcon('inventory')" 
    title="Inventory" 
/>
```

### Dropdown Navigation Menu

```blade
<x-nav-item 
    route-pattern="sales.*" 
    :icon="App\Helpers\NavigationHelper::getIcon('sales')" 
    title="Sales" 
    :is-dropdown="true">
    
    <!-- Child items -->
    <x-nav-item 
        route="sales.customers.index" 
        route-pattern="sales.customers.*"
        :icon="App\Helpers\NavigationHelper::getIcon('customers', 'w-4 h-4 mr-3')" 
        title="Customers" 
        size="small"
    />
    
    <x-nav-item 
        route="sales.returns.index" 
        route-pattern="sales.returns.*"
        :icon="App\Helpers\NavigationHelper::getIcon('returns', 'w-4 h-4 mr-3')" 
        title="Sales Return" 
        size="small"
    />
</x-nav-item>
```

### Using Custom Icons

```blade
<x-nav-item 
    href="#" 
    icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>' 
    title="Sales Order" 
    size="small"
/>
```

## Component Parameters

### `nav-item` Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `route` | string | null | Laravel route name for exact matching |
| `href` | string | null | Direct URL link |
| `route-pattern` | string | null | Route pattern for wildcard matching (e.g., 'sales.*') |
| `icon` | string | null | SVG icon HTML |
| `title` | string | '' | Display text for the navigation item |
| `is-dropdown` | boolean | false | Whether this item contains dropdown children |
| `show-dropdown` | boolean | false | Force dropdown to be open initially |
| `size` | string | 'normal' | Size variant ('normal' or 'small') |
| `active-class` | string | Default blue classes | CSS classes applied when active |
| `base-class` | string | null | Override default base CSS classes |

## NavigationHelper Methods

### Available Icons

The helper includes predefined icons for:
- `dashboard`
- `inventory`
- `products`
- `sales`
- `customers`
- `purchases`
- `reports`
- `returns`
- `integration`
- `stock-adjustment`
- `composite-products`

### Icon Usage

```php
// Get an icon with default size (w-5 h-5 mr-3)
NavigationHelper::getIcon('dashboard')

// Get an icon with custom size
NavigationHelper::getIcon('products', 'w-4 h-4 mr-3')
```

### Route Checking

```php
// Check if a single route pattern is active
NavigationHelper::isActiveRoute('sales.*')

// Check multiple route patterns
NavigationHelper::isActiveRoute(['sales.*', 'inventory.*'])

// Get active CSS class if route matches
NavigationHelper::getActiveClass('sales.*')
```

## Custom Blade Directives

### `@navIcon`

```blade
@navIcon('dashboard')
@navIcon('products', 'w-4 h-4 mr-3')
```

### `@isActiveRoute`

```blade
@if(@isActiveRoute('sales.*'))
    <!-- Content for active sales routes -->
@endif
```

### `@activeClass`

```blade
<div class="nav-item @activeClass('sales.*')">
    Sales Section
</div>
```

## Active State Logic

The component automatically detects active states using this priority:

1. **Route Pattern** - Uses Laravel's `request()->routeIs()` with wildcards
2. **Exact Route** - Matches specific route names
3. **URL Matching** - Direct URL comparison

### Examples

```blade
<!-- Will be active on any inventory route -->
<x-nav-item route-pattern="inventory.*" title="Inventory" />

<!-- Will be active only on exact dashboard route -->
<x-nav-item route="dashboard" title="Dashboard" />

<!-- Will be active on specific URL -->
<x-nav-item href="/custom-page" title="Custom Page" />
```

## Dropdown Behavior

Dropdowns automatically:
- Open when any child route is active
- Close/open with smooth animations
- Maintain state during navigation
- Show arrow rotation indicator

## Customization

### Custom Active Classes

```blade
<x-nav-item 
    route="dashboard" 
    title="Dashboard"
    active-class="bg-red-100 text-red-600 border-l-4 border-red-500"
/>
```

### Custom Base Classes

```blade
<x-nav-item 
    route="dashboard" 
    title="Dashboard"
    base-class="flex items-center px-6 py-3 text-lg font-medium"
/>
```

## Adding New Icons

To add new icons to the NavigationHelper:

1. Open `app/Helpers/NavigationHelper.php`
2. Add your icon to the `$icons` array in the `getIcon()` method:

```php
'my-new-icon' => '<svg class="' . $size . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="..."></path>
</svg>',
```

3. Use it in your navigation:

```blade
<x-nav-item 
    :icon="App\Helpers\NavigationHelper::getIcon('my-new-icon')" 
    title="My New Section" 
/>
```

## Benefits

### Before (Old System)
- Repeated `request()->routeIs()` checks
- Inconsistent styling
- Hard to maintain
- Module-specific logic

### After (New System)
- Single reusable component
- Consistent behavior
- Easy to maintain
- Scalable for new modules
- Centralized icon management
- Automatic active state detection

## Migration Guide

### Old Code
```blade
<a href="{{ route('sales.customers.index') }}"
   class="flex items-center px-4 py-2 text-sm text-gray-600 {{ request()->routeIs('sales.customers.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-500' : '' }}">
    <svg class="w-4 h-4 mr-3">...</svg>
    Customers
</a>
```

### New Code
```blade
<x-nav-item 
    route="sales.customers.index" 
    route-pattern="sales.customers.*"
    :icon="App\Helpers\NavigationHelper::getIcon('customers', 'w-4 h-4 mr-3')" 
    title="Customers" 
    size="small"
/>
```

This new system provides a much cleaner, more maintainable approach to navigation while ensuring consistent behavior across your entire Laravel application.
