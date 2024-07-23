@props(['title'])
@php 
    $darkMode = config('app.darkMode', true);
@endphp
<!DOCTYPE html>
<html 
    class="no-ng {{$darkMode && '{% theme %}'}}"
    ng-cloakx
    lang="en" 
    ng-app="cscPortal" 
    ng-controller="RootController"
    ng-resize="handleResize()" 
    ng-init="init({{ auth()->check()?'true':'false'}}, '{{$title ?? config("app.title", "Futo CSC Portal")}}')" 
    {{ $attributes }}
    custom-on-change>
    
    {{ $slot }}
</html>
