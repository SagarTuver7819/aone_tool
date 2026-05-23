---
name: Premium SaaS Intelligence
colors:
  surface: '#f8f9fa'
  surface-dim: '#d9dadb'
  surface-bright: '#f8f9fa'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f4f5'
  surface-container: '#edeeef'
  surface-container-high: '#e7e8e9'
  surface-container-highest: '#e1e3e4'
  on-surface: '#191c1d'
  on-surface-variant: '#434656'
  inverse-surface: '#2e3132'
  inverse-on-surface: '#f0f1f2'
  outline: '#737688'
  outline-variant: '#c3c5d9'
  surface-tint: '#004af1'
  primary: '#003dcb'
  on-primary: '#ffffff'
  primary-container: '#0f52ff'
  on-primary-container: '#e1e4ff'
  inverse-primary: '#b8c4ff'
  secondary: '#555f6f'
  on-secondary: '#ffffff'
  secondary-container: '#d6e0f3'
  on-secondary-container: '#596373'
  tertiary: '#005a3c'
  on-tertiary: '#ffffff'
  tertiary-container: '#007550'
  on-tertiary-container: '#72fec0'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dde1ff'
  primary-fixed-dim: '#b8c4ff'
  on-primary-fixed: '#001453'
  on-primary-fixed-variant: '#0037b9'
  secondary-fixed: '#d9e3f6'
  secondary-fixed-dim: '#bdc7d9'
  on-secondary-fixed: '#121c2a'
  on-secondary-fixed-variant: '#3d4756'
  tertiary-fixed: '#6ffbbe'
  tertiary-fixed-dim: '#4edea3'
  on-tertiary-fixed: '#002113'
  on-tertiary-fixed-variant: '#005236'
  background: '#f8f9fa'
  on-background: '#191c1d'
  surface-variant: '#e1e3e4'
typography:
  display-lg:
    fontFamily: Hanken Grotesk
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Hanken Grotesk
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Hanken Grotesk
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-sm:
    fontFamily: Hanken Grotesk
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
  headline-lg-mobile:
    fontFamily: Hanken Grotesk
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  container-margin: 2rem
  gutter: 1.5rem
  section-gap: 2.5rem
  stack-sm: 0.5rem
  stack-md: 1rem
  stack-lg: 1.5rem
---

## Brand & Style

This design system is built for high-stakes business intelligence, prioritizing clarity, precision, and an executive-level aesthetic. The brand personality is authoritative yet accessible, embodying the qualities of a trusted advisor. It targets data analysts and decision-makers who require a noise-free environment to synthesize complex information.

The visual style is **Corporate Modern with a Minimalist focus**. It utilizes generous whitespace to reduce cognitive load, high-end typography for readability, and subtle depth to organize information hierarchically. The interface should feel "light as air" but functionally dense, using fine lines and soft shadows instead of heavy borders or vibrant gradients to define structure.

## Colors

The color palette is rooted in professional reliability. The primary "Executive Blue" is used sparingly for call-to-actions and critical data points. The neutral scale is expansive, focusing on subtle off-whites and cool greys to create a layered, "paper-like" depth without harsh contrast.

- **Primary (#0F52FF):** Actionable items, focus states, and primary data series.
- **Secondary (#1F2937):** High-contrast text and grounding elements like sidebars or headers.
- **Success/Tertiary (#10B981):** Positive growth indicators and "healthy" status metrics.
- **Error (#EF4444):** Declining metrics and critical alerts.
- **Surface Palette:** Uses a range from white (#FFFFFF) to a soft grey (#F3F4F6) to differentiate containers from the background.

## Typography

The system uses **Hanken Grotesk** for headlines to provide a sharp, contemporary character that feels distinct from standard SaaS templates. **Inter** is used for body text and functional UI elements due to its exceptional legibility at small sizes and high-density data views.

Information hierarchy is established through weight and scale. Labels use a slightly increased letter-spacing and uppercase styling to distinguish metadata from content. For dashboard metrics, tabular figures (monospaced numbers) should be used within Inter to ensure column alignment in data grids.

## Layout & Spacing

This design system employs a **12-column fluid grid** with a maximum content width of 1440px for desktop screens. Layouts should prioritize vertical rhythm and consistent "breathability."

- **Desktop (1280px+):** 12 columns, 32px margins, 24px gutters.
- **Tablet (768px - 1279px):** 8 columns, 24px margins, 16px gutters.
- **Mobile (Below 768px):** 4 columns, 16px margins, 12px gutters.

The "Section-Gap" (40px) is strictly used to separate major dashboard modules, while "Stack-LG" (24px) is the standard padding for cards and containers. Internal card elements use "Stack-MD" (16px) to maintain a compact but clear internal structure.

## Elevation & Depth

Hierarchy is achieved through **Tonal Layers** and **Ambient Shadows**. Instead of using heavy borders, depth is communicated by placing white containers on a light grey background (#F9FAFB).

- **Level 0 (Background):** #F9FAFB – The base canvas.
- **Level 1 (Cards/Containers):** White background with a very soft, diffused shadow: `0 4px 20px -2px rgba(0, 0, 0, 0.05)`.
- **Level 2 (Popovers/Modals):** White background with a more pronounced elevation shadow: `0 12px 32px -4px rgba(0, 0, 0, 0.1)`.
- **Separators:** Use 1px borders in #E5E7EB for internal card divisions only when whitespace is insufficient to group content.

## Shapes

The shape language is **Rounded**, striking a balance between the precision of hard edges and the approachability of circles. This "soft-square" aesthetic feels modern and high-end.

- **Standard Components (Buttons, Inputs):** 0.5rem (8px) corner radius.
- **Cards & Large Containers:** 1rem (16px) corner radius to create a distinct frame for data sections.
- **Status Badges & Chips:** Fully pill-shaped to differentiate them from interactive buttons.

## Components

### Buttons
Primary buttons use a solid Executive Blue fill with white text. Secondary buttons use a subtle grey-scale ghost style (borderless with a light grey hover state). All buttons feature a 300ms transition on hover for a polished feel.

### Cards
Dashboard cards are the primary container. They must include a standard 24px internal padding. Title areas within cards should be separated by a subtle 1px divider or generous whitespace.

### Inputs & Selects
Input fields use a 1px #D1D5DB border that transitions to Executive Blue on focus. Use "Inter" at 14px for input text to maximize space in dense forms.

### Data Tables
Tables are the heart of the system. Use "zebra striping" with a very faint #F9FAFB fill on even rows. Headers should be sticky, using `label-md` typography and a 1px bottom border.

### Metrics & Sparklines
Key Performance Indicators (KPIs) should use `headline-lg` for the value and `body-sm` for the label. Accompanying sparklines should be simplified, using the Primary or Success/Error colors with a subtle area fill beneath the stroke.