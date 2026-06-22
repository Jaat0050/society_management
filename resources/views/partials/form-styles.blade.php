<style>
    /* Override Tailwind preflight — all panel form controls */
    .form-field label,
    label.form-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.5rem;
        letter-spacing: 0.01em;
    }

    .form-field {
        margin-bottom: 0;
    }

    .form-control,
    .form-select,
    .form-field input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="hidden"]),
    .form-field select,
    .form-field textarea,
    .form-card input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="hidden"]),
    .form-card select,
    .form-card textarea,
    main input[type="text"]:not(.no-form-style),
    main input[type="email"]:not(.no-form-style),
    main input[type="password"]:not(.no-form-style),
    main input[type="number"]:not(.no-form-style),
    main input[type="search"]:not(.no-form-style),
    main input[type="tel"]:not(.no-form-style),
    main input[type="date"]:not(.no-form-style),
    main input[type="datetime-local"]:not(.no-form-style),
    main select:not(.no-form-style),
    main textarea:not(.no-form-style) {
        display: block !important;
        width: 100% !important;
        min-height: 2.75rem !important;
        border: 1.5px solid #94a3b8 !important;
        background-color: #f8fafc !important;
        border-radius: 0.625rem !important;
        padding: 0.625rem 0.875rem !important;
        font-size: 0.875rem !important;
        line-height: 1.25rem !important;
        color: #0f172a !important;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04) !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease !important;
        -webkit-appearance: none !important;
        appearance: none !important;
    }

    .form-field textarea,
    .form-card textarea,
    main textarea:not(.no-form-style) {
        min-height: 5rem !important;
        resize: vertical !important;
    }

    .form-select,
    .form-field select,
    .form-card select,
    main select:not(.no-form-style) {
        cursor: pointer !important;
        padding-right: 2.5rem !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 0.65rem center !important;
        background-size: 1.25rem !important;
        background-color: #f8fafc !important;
    }

    .form-control:hover,
    .form-select:hover,
    .form-field input:not([type="checkbox"]):hover,
    .form-field select:hover,
    .form-field textarea:hover,
    .form-card input:not([type="checkbox"]):hover,
    .form-card select:hover,
    .form-card textarea:hover,
    main input:hover:not(.no-form-style),
    main select:hover:not(.no-form-style),
    main textarea:hover:not(.no-form-style) {
        border-color: #64748b !important;
        background-color: #ffffff !important;
    }

    .form-control:focus,
    .form-select:focus,
    .form-field input:not([type="checkbox"]):focus,
    .form-field select:focus,
    .form-field textarea:focus,
    .form-card input:not([type="checkbox"]):focus,
    .form-card select:focus,
    .form-card textarea:focus,
    main input:focus:not(.no-form-style),
    main select:focus:not(.no-form-style),
    main textarea:focus:not(.no-form-style) {
        outline: none !important;
        border-color: #4f46e5 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.18), inset 0 1px 2px rgba(15, 23, 42, 0.04) !important;
    }

    .form-control::placeholder,
    .form-field input::placeholder,
    .form-field textarea::placeholder,
    main input::placeholder,
    main textarea::placeholder {
        color: #94a3b8 !important;
        opacity: 1 !important;
    }

    .form-control:disabled,
    .form-select:disabled {
        opacity: 0.6 !important;
        cursor: not-allowed !important;
        background-color: #e2e8f0 !important;
    }

    input[type="checkbox"] {
        width: 1.125rem !important;
        height: 1.125rem !important;
        min-height: auto !important;
        border: 1.5px solid #94a3b8 !important;
        border-radius: 0.25rem !important;
        accent-color: #4f46e5 !important;
        cursor: pointer !important;
    }

    /* Form card */
    .form-card {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 24px rgba(15, 23, 42, 0.06);
    }

    .form-card-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }

    .form-card-subtitle {
        font-size: 0.8125rem;
        color: #64748b;
        margin-bottom: 1.25rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }

    @media (min-width: 768px) {
        .form-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .form-grid .span-full {
            grid-column: 1 / -1;
        }
    }

    .form-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding-top: 0.5rem;
        border-top: 1px solid #e2e8f0;
        margin-top: 0.5rem;
    }

    .form-actions.span-full {
        grid-column: 1 / -1;
    }

    .btn-primary {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        min-height: 2.75rem !important;
        width: auto !important;
        border: none !important;
        border-radius: 0.625rem !important;
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
        padding: 0.625rem 1.25rem !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35) !important;
        cursor: pointer !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease !important;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%) !important;
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4) !important;
    }

    .btn-secondary {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        min-height: 2.75rem !important;
        width: auto !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 0.625rem !important;
        background-color: #ffffff !important;
        padding: 0.625rem 1.25rem !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: #475569 !important;
        cursor: pointer !important;
    }

    .btn-secondary:hover {
        background-color: #f1f5f9 !important;
        border-color: #94a3b8 !important;
    }

    .search-box {
        max-width: 16rem !important;
        width: 16rem !important;
    }

    .field-error {
        display: block;
        margin-top: 0.375rem;
        font-size: 0.75rem;
        color: #dc2626;
        font-weight: 500;
    }

    .field-required::after {
        content: " *";
        color: #ef4444;
    }
</style>
