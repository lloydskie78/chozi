<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Transaction;

class SecurityMiddleware
{
    /**
     * Handle an incoming request with security measures
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Input sanitization
        $this->sanitizeInput($request);
        
        // 2. Detect suspicious patterns
        $this->detectSuspiciousActivity($request);
        
        // 3. Validate HTTPS in production
        if (config('app.env') === 'production' && !$request->secure()) {
            return redirect()->secure($request->getRequestUri());
        }
        
        // Process the request
        $response = $next($request);
        
        // 4. Add security headers
        $response = $this->addSecurityHeaders($response);
        
        return $response;
    }
    
    /**
     * Sanitize input data
     */
    private function sanitizeInput(Request $request)
    {
        // Get all input data
        $input = $request->all();
        
        // Recursively sanitize all string inputs
        $sanitized = $this->recursiveSanitize($input);
        
        // Replace the input with sanitized data
        $request->replace($sanitized);
    }
    
    /**
     * Recursively sanitize input
     */
    private function recursiveSanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'recursiveSanitize'], $data);
        }
        
        if (is_string($data)) {
            // Remove potential XSS patterns
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
            
            // Remove potential SQL injection patterns
            $data = str_replace(['<script', '</script>', 'javascript:', 'vbscript:', 'onload=', 'onerror='], '', $data);
            
            // Remove other dangerous patterns
            $data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $data);
        }
        
        return $data;
    }
    
    /**
     * Detect suspicious activity patterns
     */
    private function detectSuspiciousActivity(Request $request)
    {
        $suspiciousPatterns = [
            // SQL Injection patterns
            '/(\s*(union|select|insert|update|delete|drop|create|alter)\s+)/i',
            '/(\s*(or|and)\s+\d+\s*=\s*\d+)/i',
            '/(\s*[\'"`]\s*(or|and)\s*[\'"`]\s*=\s*[\'"`])/i',
            
            // XSS patterns
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript\s*:/i',
            '/on\w+\s*=\s*[\'"][^\'"]*/i',
            
            // Path traversal
            '/\.\.\//',
            '/\.\.\\\\/',
            
            // Command injection
            '/[;&|`$()]/',
        ];
        
        $userAgent = $request->userAgent();
        $inputData = json_encode($request->all());
        $uri = $request->getRequestUri();
        
        // Check for suspicious patterns
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $inputData) || preg_match($pattern, $uri)) {
                $this->logSuspiciousActivity($request, 'Suspicious pattern detected', $pattern);
                break;
            }
        }
        
        // Check for suspicious user agents
        $suspiciousAgents = ['sqlmap', 'nikto', 'nmap', 'masscan', 'nessus', 'burp'];
        foreach ($suspiciousAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                $this->logSuspiciousActivity($request, 'Suspicious user agent', $agent);
                break;
            }
        }
        
        // Check for excessive parameters (potential parameter pollution)
        if (count($request->all()) > 50) {
            $this->logSuspiciousActivity($request, 'Excessive parameters', 'Parameter count: ' . count($request->all()));
        }
        
        // Check for large request size
        if ($request->header('Content-Length') > 10485760) { // 10MB
            $this->logSuspiciousActivity($request, 'Large request size', 'Size: ' . $request->header('Content-Length'));
        }
    }
    
    /**
     * Log suspicious activity
     */
    private function logSuspiciousActivity(Request $request, $reason, $details = null)
    {
        $transaction = Transaction::log(
            'security_event',
            'Suspicious activity detected',
            $reason . ($details ? ": {$details}" : ''),
            [
                'uri' => $request->getRequestUri(),
                'method' => $request->method(),
                'input' => $request->all(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->header('referer'),
                'reason' => $reason,
                'details' => $details
            ],
            auth()->user(),
            null,
            Transaction::SEVERITY_WARNING
        );
        
        $transaction->markAsSuspicious($reason);
        
        // In production, you might want to block the request or rate limit the IP
        // For now, we just log it
    }
    
    /**
     * Add security headers to response
     */
    private function addSecurityHeaders($response)
    {
        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');
        
        // Enable XSS protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Enforce HTTPS
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        
        // Content Security Policy
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; " .
               "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; " .
               "font-src 'self' https://fonts.gstatic.com; " .
               "img-src 'self' data: https:; " .
               "connect-src 'self'; " .
               "frame-ancestors 'none';";
        
        $response->headers->set('Content-Security-Policy', $csp);
        
        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions Policy
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        return $response;
    }
}
