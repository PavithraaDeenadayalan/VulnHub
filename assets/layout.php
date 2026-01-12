<?php
/**
 * Shared Layout Component
 * 
 * Provides dark mode styling and consistent layout
 */

function get_dark_mode_styles() {
    return '
    <style>
        :root {
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --bg-tertiary: #3d3d3d;
            --text-primary: #e0e0e0;
            --text-secondary: #b0b0b0;
            --accent-primary: #667eea;
            --accent-secondary: #764ba2;
            --border-color: #404040;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
        }
        
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
        }
        
        .main-container {
            flex: 1;
            margin-left: 280px;
            padding: 20px;
            transition: margin-left 0.3s;
        }
        
        .sidebar {
            position: fixed;
            left: 0;
            top: 50px;
            width: 280px;
            height: calc(100vh - 50px);
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-tertiary);
        }
        
        .sidebar-header h2 {
            color: var(--text-primary);
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .flag-counter {
            background: var(--bg-primary);
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .flag-counter strong {
            color: var(--warning);
            font-size: 16px;
        }
        
        .vuln-nav {
            padding: 10px 0;
        }
        
        .vuln-item {
            border-bottom: 1px solid var(--border-color);
        }
        
        .vuln-item.active {
            background: var(--bg-tertiary);
        }
        
        .vuln-header {
            padding: 12px 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s;
        }
        
        .vuln-header:hover {
            background: var(--bg-tertiary);
        }
        
        .vuln-code {
            font-weight: bold;
            color: var(--accent-primary);
            min-width: 40px;
        }
        
        .vuln-name {
            flex: 1;
            color: var(--text-primary);
            font-size: 14px;
        }
        
        .toggle-icon {
            color: var(--text-secondary);
            font-size: 12px;
        }
        
        .coming-soon {
            font-size: 11px;
            color: var(--text-secondary);
            font-style: italic;
        }
        
        .vuln-item.disabled .vuln-header {
            cursor: not-allowed;
            opacity: 0.5;
        }
        
        .vuln-levels {
            background: var(--bg-primary);
            padding: 5px 0;
        }
        
        .level-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px 10px 50px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        
        .level-link:hover {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border-left-color: var(--accent-primary);
        }
        
        .level-link.active {
            background: var(--bg-tertiary);
            color: var(--accent-primary);
            border-left-color: var(--accent-primary);
            font-weight: bold;
        }
        
        .flag-badge {
            background: var(--success);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px;
            border-top: 1px solid var(--border-color);
            background: var(--bg-secondary);
            display: flex;
            gap: 10px;
        }
        
        .dashboard-link, .logout-link {
            flex: 1;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.2s;
            font-size: 14px;
        }
        
        .dashboard-link {
            background: var(--accent-primary);
            color: white;
        }
        
        .dashboard-link:hover {
            background: #5568d3;
        }
        
        .logout-link {
            background: var(--danger);
            color: white;
        }
        
        .logout-link:hover {
            background: #c82333;
        }
        
        .content-wrapper {
            background: var(--bg-secondary);
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
        }
        
        .header {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
        }
        
        .level-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .level-badge.low { background: var(--danger); color: white; }
        .level-badge.medium { background: var(--warning); color: #333; }
        .level-badge.hard { background: #ff9800; color: white; }
        .level-badge.impossible { background: var(--danger); color: white; }
        
        h1 {
            color: var(--text-primary);
            margin-bottom: 10px;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 10px;
            color: var(--accent-primary);
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .vulnerability-info {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid var(--warning);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .vulnerability-info h3 {
            color: var(--warning);
            margin-bottom: 10px;
        }
        
        .vulnerability-info p {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 8px;
        }
        
        .flag-success {
            background: rgba(40, 167, 69, 0.1);
            border: 2px solid var(--success);
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
            animation: slideIn 0.5s ease-out;
        }
        
        .flag-success h3 {
            color: var(--success);
            margin-bottom: 10px;
            font-size: 20px;
        }
        
        .flag-success .flag-code {
            background: var(--bg-primary);
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            font-family: monospace;
            font-size: 18px;
            color: var(--success);
            font-weight: bold;
            letter-spacing: 2px;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-primary);
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="password"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            color: var(--text-primary);
            font-size: 14px;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--accent-primary);
        }
        
        button, .btn {
            padding: 12px 24px;
            background: var(--accent-primary);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: background 0.2s;
        }
        
        button:hover:not(:disabled), .btn:hover:not(:disabled) {
            background: #5568d3;
        }
        
        button:disabled {
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            cursor: not-allowed;
        }
        
        .message {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid var(--success);
        }
        
        .error {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid var(--danger);
        }
        
        .info-box {
            background: var(--bg-tertiary);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid var(--info);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-container {
                margin-left: 0;
            }
        }
    </style>
    ';
}
?>
