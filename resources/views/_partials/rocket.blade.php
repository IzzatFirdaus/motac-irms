{{-- rocket.blade.php --}}
{{--
    Animated Rocket Component.
    Consider its placement carefully to ensure it aligns with the
    "Clean & Modern Official Aesthetic" and "Formal Tone" of the MOTAC system.
    It might be suitable for specific contexts like success pages or guided onboarding,
    but potentially too playful for general UI elements.

    Colors have been changed to use CSS variables. Ensure these variables
    (--motac-primary, --motac-secondary, --motac-background-dark, --motac-accent-fire-primary, etc.)
    are defined in your global MOTAC theme stylesheet.
--}}

<style>
    :root {
        /* Define MOTAC theme color variables here or ensure they are globally available.
       These are placeholders; replace with your actual MOTAC theme variables.
       The Design Language Doc specifies:
       Primary: #0055A4 (light), #3D8FD1 (dark)
       Secondary: #8C1D40 (light), #A9496B (dark)
       Background (Dark for this component's original context): #121826 (from DL Doc dark mode bg)
       Surface (Dark for this component's original context): #1E293B (from DL Doc dark mode surface)

       For fire/accent colors, you might need new theme variables or map existing ones.
    */
        --rocket-bg: var(--motac-background-dark, #121826);
        /* Using dark background from DL Doc */
        --rocket-wing-capsule: var(--motac-secondary, #8C1D40);
        /* Example mapping to MOTAC Secondary */
        --rocket-body-main: var(--motac-surface-light, #F3F3F3);
        /* Light surface for rocket body */
        --rocket-body-shadow: var(--motac-border, #E0E0E0);
        /* A border/shadow color */
        --rocket-window-inner: var(--motac-text-dark-bg, #272425);
        /* Darker element for contrast */
        --rocket-fire-primary: var(--motac-warning-light, #EF8B32);
        /* Example: map to a warning/accent */
        --rocket-fire-secondary: var(--motac-critical-light, #E82134);
        /* Example: map to critical/accent */
        --rocket-star-color: var(--motac-surface-light, #FFFFFF);
        /* Stars */
    }

    /* Ensure the parent of .scene provides the intended background if not the body */
    .rocket-body-background-scope {
        /* Add this class to a parent div if body bg isn't correct */
        background: var(--rocket-bg);
        padding: 20px;
        /* Original padding */
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
        /* animation will be controlled by prefers-reduced-motion */
    }

    .rocket-scene.animated {
        /* Add .animated class to enable animations by default */
        animation: rocket-vibration 0.2s infinite;
    }

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
        /* MOTAC Color */
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
        /* MOTAC Color */
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
        /* MOTAC Color */
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
        /* MOTAC Color */
        border-bottom: 45px solid transparent;
    }

    .exhaust {
        position: absolute;
        z-index: 20;
        top: 156px;
        left: 51px;
        height: 0px;
        width: 101px;
        border-top: 23px solid var(--rocket-wing-capsule);
        /* MOTAC Color */
        border-left: 9px solid transparent;
        border-right: 8px solid transparent;
    }

    .capsule {
        position: absolute;
        z-index: 30;
        background: var(--rocket-bg);
        /* MOTAC Color */
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
        /* Using single color and a pseudo-element for shadow if needed, or a subtle gradient */
        background: var(--rocket-body-main);
        /* MOTAC Color */
        /* Simpler gradient for MOTAC theme:
    background: linear-gradient(to right, var(--rocket-body-main) 0%, var(--rocket-body-main) 65%, var(--rocket-body-shadow) 65%, var(--rocket-body-shadow) 100%);
    */
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
        /* MOTAC Color */
    }

    .capsule .top:after {
        /* This creates a shadow/cutout effect */
        content: "";
        position: absolute;
        height: 0px;
        width: 0px;
        border-left: 0px solid transparent;
        border-right: 156px solid transparent;
        border-bottom: 84px solid var(--rocket-bg);
        /* MOTAC Color */
        transform: skew(42deg);
        top: -14px;
        left: 25px;
        z-index: 50;
    }

    .capsule .top .shadow {
        /* This is a shading effect on the cone */
        position: absolute;
        height: 0px;
        width: 0px;
        border-left: 20px solid transparent;
        border-right: 80px solid transparent;
        border-bottom: 90px solid var(--rocket-body-shadow);
        /* MOTAC Color */
        transform: skew(26deg);
        top: -20px;
        left: -3px;
        z-index: 40;
        box-shadow: none !important;
        /* Removed original shadow */
    }

    .window-big {
        width: 70px;
        height: 70px;
        background: var(--rocket-wing-capsule);
        /* MOTAC Color */
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
        /* MOTAC Color */
        border-radius: 8em;
        position: absolute;
        z-index: 50;
        top: 70px;
        left: 79px;
    }

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
        /* MOTAC Colors */
    }

    .fire-2 {
        display: none;
        /* Kept as original, can be enabled if desired */
        position: absolute;
        height: 55px;
        width: 55px;
        top: 180px;
        transform-origin: 50% 50%;
        transform: rotate(-33deg) skew(0deg, -30deg);
        z-index: 15;
        left: 58px;
        background: linear-gradient(135deg, var(--rocket-fire-secondary) 0%, var(--rocket-fire-secondary) 50%, var(--rocket-fire-primary) 50%, var(--rocket-fire-primary) 100%);
        /* MOTAC Colors */
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
        /* MOTAC Colors */
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
        /* MOTAC Colors */
    }

    .spark-1,
    .spark-2,
    .spark-3,
    .spark-4 {
        background: var(--rocket-fire-primary);
        /* MOTAC Color */
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

    /* Different color for variety */
    .spark-4 {
        bottom: 20px;
        left: 83px;
        width: 10px;
        height: 10px;
        animation-delay: 0.16s;
    }

    .star {
        position: absolute;
        width: 4px;
        height: 20px;
        background: var(--rocket-star-color);
        /* MOTAC Color */
        z-index: 5;
        /* Lowered z-index to be behind rocket potentially */
    }

    /* Star positions remain the same */
    .star.star--1 {
        left: 50px;
        top: -10px;
        animation-delay: 0.1s;
    }

    .star.star--2 {
        right: 60px;
        top: 30px;
        animation-delay: 0.1s;
    }

    .star.star--3 {
        top: 80px;
        left: 25px;
        animation-delay: 0.2s;
    }

    .star.star--4 {
        top: -20px;
        right: 75px;
        animation-delay: 0.2s;
    }

    .star.star--5 {
        right: 30px;
        top: -60px;
        animation-delay: 0.3s;
    }

    .star.star--6 {
        right: 160px;
        top: 50px;
        animation-delay: 0.4s;
    }

    .star.star--7 {
        top: 20px;
        left: 75px;
        animation-delay: 0.3s;
    }

    .star.star--8 {
        top: -30px;
        right: 95px;
        animation-delay: 0.4s;
    }

    .star.star--9 {
        right: 30px;
        top: -60px;
        animation-delay: 0.3s;
    }

    .star.star--10 {
        right: 160px;
        top: 50px;
        animation-delay: 0.4s;
    }

    .star.star--11 {
        top: 20px;
        left: 75px;
        animation-delay: 0.3s;
    }

    .star.star--12 {
        top: -30px;
        right: 95px;
        animation-delay: 0.4s;
    }

    .star.star--13 {
        left: -30px;
        top: -60px;
        animation-delay: 0.3s;
    }

    .star.star--14 {
        right: -20px;
        top: 50px;
        animation-delay: 0.4s;
    }

    .star.star--15 {
        top: 20px;
        left: -75px;
        animation-delay: 0.3s;
    }

    .star.star--16 {
        top: -30px;
        right: -95px;
        animation-delay: 0.4s;
    }


    /* Animations */
    .rocket-scene.animated .propulsed__slow,
    .rocket-scene.animated .fire-4,
    .rocket-scene.animated .fire-3,
    .rocket-scene.animated .fire-2 {
        animation: fire_propulsion 0.3s ease-in infinite;
    }

    .rocket-scene.animated .main_fire,
    .rocket-scene.animated .fire-1 {
        animation: main_fire 0.1s cubic-bezier(0.175, 0.885, 0.42, 1.41) infinite;
    }

    .rocket-scene.animated .propulsed,
    .rocket-scene.animated .spark-4,
    .rocket-scene.animated .spark-3,
    .rocket-scene.animated .spark-2,
    .rocket-scene.animated .spark-1 {
        animation: dancing_fire 0.24s infinite;
    }

    .rocket-scene.animated .hyperspace,
    .rocket-scene.animated .star {
        animation: hyperspace 0.4s infinite;
    }

    @keyframes dancing_fire {
        0% {
            transform-origin: 50% 50%;
            transform: translate(0, -10px) scale(1);
            opacity: 1;
        }

        100% {
            transform: translate(0, 50px) scale(1);
            opacity: 0;
        }
    }

    @keyframes fire_propulsion {
        0% {
            transform: translate(0, -10px) scale(1) rotate(-33deg) skew(0deg, -30deg);
            transform-origin: 50% 50%;
            opacity: 1;
        }

        100% {
            transform: translate(0, 50px) scale(0.7) rotate(-33deg) skew(0deg, -30deg);
            opacity: 0;
        }
    }

    @keyframes main_fire {
        0% {
            transform: translate(0, 5px) scale(1.1, 1) rotate(-33deg) skew(0deg, -30deg);
        }

        100% {
            transform: translate(0, 0px) scale(1, 1.4) rotate(-33deg) skew(0deg, -30deg);
        }
    }

    @keyframes rocket-vibration {

        /* Renamed for clarity */
        0% {
            transform: scale(1) translate(0, 0) rotate(45deg);
        }

        50% {
            transform: scale(1) translate(1px, -1px) rotate(45deg);
        }

        100% {
            transform: scale(1) translate(0, 0) rotate(45deg);
        }
    }

    @keyframes hyperspace {
        0% {
            transform: translate(0, -100px) scale(1, 0);
            opacity: 1;
        }

        100% {
            transform: translate(0, 400px) scale(1, 1);
            opacity: 0;
        }
    }

    /* Accessibility: Reduced Motion */
    @media (prefers-reduced-motion: reduce) {

        .rocket-scene.animated,
        .rocket-scene.animated .propulsed__slow,
        .rocket-scene.animated .fire-4,
        .rocket-scene.animated .fire-3,
        .rocket-scene.animated .fire-2,
        .rocket-scene.animated .main_fire,
        .rocket-scene.animated .fire-1,
        .rocket-scene.animated .propulsed,
        .rocket-scene.animated .spark-4,
        .rocket-scene.animated .spark-3,
        .rocket-scene.animated .spark-2,
        .rocket-scene.animated .spark-1,
        .rocket-scene.animated .hyperspace,
        .rocket-scene.animated .star {
            animation: none !important;
            /* Disable all animations */
        }

        /* You might want a very subtle static effect for reduced motion, e.g., a slight glow */
        .rocket-scene.animated .fire-1 {
            /* Example: static main fire */
            transform: translate(0, 2px) scale(1, 1.2) rotate(-33deg) skew(0deg, -30deg);
            opacity: 0.8;
        }
    }
</style>

{{-- Add this class to a parent div if body bg isn't the correct scope --}}
{{-- <div class="rocket-body-background-scope"> --}}
<div class="rocket-scene animated"> {{-- Added 'animated' class to control animations via CSS --}}
    <div class="wing-left"></div>
    <div class="wing-right"></div>
    <div class="exhaust"></div>
    <div class="capsule">
        <div class="top">
            <div class="shadow"></div>
        </div>
        <div class="base"></div>
    </div>
    <div class="window-big"></div>
    <div class="window-small"></div>
    <div class="fire-1 main_fire"></div> {{-- Added main_fire for consistency if it's the primary fire effect --}}
    <div class="fire-2 propulsed__slow"></div>
    <div class="fire-3 propulsed__slow"></div>
    <div class="fire-4 propulsed__slow"></div>
    <div class="spark-1 propulsed"></div>
    <div class="spark-2 propulsed"></div>
    <div class="spark-3 propulsed"></div>
    <div class="spark-4 propulsed"></div>
    <div class="star star--1 hyperspace"></div>
    <div class="star star--2 hyperspace"></div>
    <div class="star star--3 hyperspace"></div>
    <div class="star star--4 hyperspace"></div>
    <div class="star star--5 hyperspace"></div>
    <div class="star star--6 hyperspace"></div>
    <div class="star star--7 hyperspace"></div>
    <div class="star star--8 hyperspace"></div>
    <div class="star star--9 hyperspace"></div>
    <div class="star star--10 hyperspace"></div>
    <div class="star star--11 hyperspace"></div>
    <div class="star star--12 hyperspace"></div>
    <div class="star star--13 hyperspace"></div>
    <div class="star star--14 hyperspace"></div>
    <div class="star star--15 hyperspace"></div>
    <div class="star star--16 hyperspace"></div>
</div>
{{-- </div> --}}
