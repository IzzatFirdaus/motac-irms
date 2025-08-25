{{-- resources/views/_partials/rocket.blade.php --}}
{{--
    Animated Rocket Component for MOTAC System

    This component provides an animated rocket illustration suitable for:
    - Success pages
    - Loading states
    - Achievement celebrations
    - Onboarding flows

    Features:
    - Responsive design
    - MOTAC color theme integration
    - Accessibility support (respects prefers-reduced-motion)
    - CSS variables for easy theming

    Usage considerations:
    - Align with "Clean & Modern Official Aesthetic"
    - Use sparingly to maintain "Formal Tone"
    - Consider context appropriateness

    The colors use CSS variables that should be defined in your global MOTAC theme.
--}}

<style>
    :root {
        /* MOTAC theme color variables for rocket component
           These should match your global theme variables */
        --rocket-bg: var(--motac-background-dark, #121826);
        --rocket-wing-capsule: var(--motac-secondary, #8C1D40);
        --rocket-body-main: var(--motac-surface-light, #F3F3F3);
        --rocket-body-shadow: var(--motac-border, #E0E0E0);
        --rocket-window-inner: var(--motac-text-dark-bg, #272425);
        --rocket-fire-primary: var(--motac-warning-light, #EF8B32);
        --rocket-fire-secondary: var(--motac-critical-light, #E82134);
        --rocket-star-color: var(--motac-surface-light, #FFFFFF);
    }

    /* Optional background scope class */
    .rocket-body-background-scope {
        background: var(--rocket-bg);
        padding: 20px;
    }

    .rocket-scene *,
    .rocket-scene *:before,
    .rocket-scene *:after {
        box-sizing: border-box;
    }

    .rocket-scene {
        width: 202px;
        height: 380px;
        margin: auto;
    }

    /* Animation control - only animate when .animated class is present */
    .rocket-scene.animated {
        animation: rocket-vibration 0.2s infinite;
    }

    /* Rocket wing components */
    .wing-left {
        position: absolute;
        z-index: 10;
        height: 103px;
        width: 0px;
        padding: 0px;
        top: 82px;
        left: 16px;
        transform: rotate(10deg) skew(5deg);
        border-top: 21px solid transparent;
        border-right: 38px solid var(--rocket-wing-capsule);
        border-bottom: 19px solid transparent;
    }

    .wing-left:after {
        content: "";
        display: block;
        position: absolute;
        bottom: -50px;
        height: 0px;
        width: 0px;
        border-top: 20px solid transparent;
        border-right: 50px solid var(--rocket-bg);
        border-bottom: 50px solid transparent;
    }

    .wing-right {
        position: absolute;
        z-index: 10;
        height: 103px;
        width: 0px;
        padding: 0px;
        top: 62px;
        right: 17px;
        transform: rotate(-10deg) skew(-5deg);
        border-top: 0 solid transparent;
        border-right: 40px solid var(--rocket-wing-capsule);
        border-bottom: 15px solid transparent;
    }

    .wing-right:after {
        content: "";
        display: block;
        position: absolute;
        top: -33px;
        left: -19px;
        height: 0px;
        width: 0px;
        border-top: 36px solid transparent;
        border-right: 68px solid var(--rocket-bg);
        border-bottom: 45px solid transparent;
    }

    /* Rocket exhaust and body components */
    .exhaust {
        position: absolute;
        z-index: 20;
        top: 156px;
        left: 51px;
        height: 0px;
        width: 101px;
        border-top: 23px solid var(--rocket-wing-capsule);
        border-left: 9px solid transparent;
        border-right: 8px solid transparent;
    }

    .capsule {
        position: absolute;
        z-index: 30;
        background: var(--rocket-bg);
        left: 46px;
        top: 5px;
        width: 111px;
        height: 156px;
        opacity: 1;
        overflow: hidden;
    }

    .capsule .base {
        position: absolute;
        width: 112px;
        height: 94px;
        top: 62px;
        left: 0px;
        background: var(--rocket-body-main);
    }

    .capsule .top {
        position: absolute;
        height: 0px;
        width: 0px;
        padding: 0px;
        left: 0;
        border-left: 56px solid transparent;
        border-right: 56px solid transparent;
        border-bottom: 62px solid var(--rocket-body-main);
    }

    .capsule .top:after {
        content: "";
        position: absolute;
        height: 0px;
        width: 0px;
        border-left: 0px solid transparent;
        border-right: 156px solid transparent;
        border-bottom: 84px solid var(--rocket-bg);
        transform: skew(42deg);
        top: -14px;
        left: 25px;
        z-index: 50;
    }

    .capsule .top .shadow {
        position: absolute;
        height: 0px;
        width: 0px;
        border-left: 20px solid transparent;
        border-right: 80px solid transparent;
        border-bottom: 90px solid var(--rocket-body-shadow);
        transform: skew(26deg);
        top: -20px;
        left: -3px;
        z-index: 40;
        box-shadow: none !important;
    }

    /* Rocket window components */
    .window-big {
        width: 70px;
        height: 70px;
        background: var(--rocket-wing-capsule);
        border-radius: 8em;
        position: absolute;
        z-index: 40;
        top: 57px;
        left: 66px;
    }

    .window-small {
        width: 44px;
        height: 44px;
        background: var(--rocket-window-inner);
        border-radius: 8em;
        position: absolute;
        z-index: 50;
        top: 70px;
        left: 79px;
    }

    /* Fire effect components */
    .fire-1 {
        position: absolute;
        height: 70px;
        width: 70px;
        top: 169px;
        transform-origin: 50% 50%;
        transform: rotate(-40deg) skew(1deg, -11deg);
        z-index: 10;
        left: 64px;
        background: linear-gradient(135deg, var(--rocket-fire-primary) 0%, var(--rocket-fire-primary) 50%, var(--rocket-fire-secondary) 50%, var(--rocket-fire-secondary) 100%);
    }

    .fire-2 {
        display: none; /* Optional fire effect - can be enabled if desired */
        position: absolute;
        height: 55px;
        width: 55px;
        top: 180px;
        transform-origin: 50% 50%;
        transform: rotate(-33deg) skew(0deg, -30deg);
        z-index: 15;
        left: 58px;
        background: linear-gradient(135deg, var(--rocket-fire-secondary) 0%, var(--rocket-fire-secondary) 50%, var(--rocket-fire-primary) 50%, var(--rocket-fire-primary) 100%);
        animation-delay: 0.2s;
    }

    .fire-3 {
        position: absolute;
        height: 22px;
        width: 22px;
        top: 196px;
        left: 58px;
        transform-origin: 50% 50%;
        transform: rotate(-33deg) skew(0deg, -30deg);
        z-index: 20;
        background: linear-gradient(135deg, var(--rocket-fire-primary) 0%, var(--rocket-fire-primary) 50%, var(--rocket-fire-secondary) 50%, var(--rocket-fire-secondary) 100%);
        animation-delay: 0.2s;
    }

    .fire-4 {
        position: absolute;
        height: 22px;
        width: 22px;
        top: 200px;
        transform-origin: 50% 50%;
        transform: rotate(-33deg) skew(0deg, -30deg);
        z-index: 20;
        left: 126px;
        background: linear-gradient(135deg, var(--rocket-fire-secondary) 0%, var(--rocket-fire-secondary) 50%, var(--rocket-fire-primary) 50%, var(--rocket-fire-primary) 100%);
    }

    /* Spark effects */
    .spark-1,
    .spark-2,
    .spark-3,
    .spark-4 {
        background: var(--rocket-fire-primary);
        position: absolute;
        z-index: 20;
    }

    .spark-1 {
        bottom: 177px;
        right: 70px;
        width: 12px;
        height: 12px;
        transform-origin: 50% 50%;
    }

    .spark-2 {
        bottom: 147px;
        left: 52px;
        width: 10px;
        height: 10px;
        transform: rotate(45deg);
        animation-delay: 0.22s;
    }

    .spark-3 {
        bottom: 90px;
        left: 109px;
        width: 10px;
        height: 10px;
        transform: rotate(45deg);
        background: var(--rocket-fire-secondary);
        animation-delay: 0.32s;
    }

    .spark-4 {
        bottom: 20px;
        left: 83px;
        width: 10px;
        height: 10px;
        animation-delay: 0.16s;
    }

    /* Star background elements */
    .star {
        position: absolute;
        width: 4px;
        height: 20px;
        background: var(--rocket-star-color);
        z-index: 5;
    }

    /* Star positioning */
    .star.star--1 { left: 50px; top: -10px; animation-delay: 0.1s; }
    .star.star--2 { right: 60px; top: 30px; animation-delay: 0.1s; }
    .star.star--3 { top: 80px; left: 25px; animation-delay: 0.2s; }
    .star.star--4 { top: -20px; right: 75px; animation-delay: 0.2s; }
    .star.star--5 { right: 30px; top: -60px; animation-delay: 0.3s; }
    .star.star--6 { right: 160px; top: 50px; animation-delay: 0.4s; }
    .star.star--7 { top: 20px; left: 75px; animation-delay: 0.3s; }
    .star.star--8 { top: -30px; right: 95px; animation-delay: 0.4s; }
    .star.star--9 { right: 30px; top: -60px; animation-delay: 0.3s; }
    .star.star--10 { right: 160px; top: 50px; animation-delay: 0.4s; }
    .star.star--11 { top: 20px; left: 75px; animation-delay: 0.3s; }
    .star.star--12 { top: -30px; right: 95px; animation-delay: 0.4s; }
    .star.star--13 { left: -30px; top: -60px; animation-delay: 0.3s; }
    .star.star--14 { right: -20px; top: 50px;
