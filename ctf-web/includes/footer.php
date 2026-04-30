<?php
// File: includes/footer.php
// Reusable footer untuk semua halaman
?>

    </main> <!-- Close main from header -->
    
    <!-- Footer -->
    <footer class="bg-ctf-darker border-t border-ctf-accent/20 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-ctf-accent font-bold mb-3">🚩 CTF Platform</h3>
                    <p class="text-gray-400 text-sm">
                        Platform pembelajaran cybersecurity melalui Capture The Flag challenges.
                        Belajar hacking secara etis dan legal.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-ctf-accent font-bold mb-3">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="/ctf-web/" class="text-gray-400 hover:text-ctf-accent transition">
                                Home
                            </a>
                        </li>
                        <li>
                            <a href="/ctf-web/dashboard.php" class="text-gray-400 hover:text-ctf-accent transition">
                                Challenges
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-ctf-accent transition">
                                Leaderboard
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Info -->
                <div>
                    <h3 class="text-ctf-accent font-bold mb-3">Info</h3>
                    <p class="text-gray-400 text-sm mb-2">
                        📚 Dibuat untuk pembelajaran
                    </p>
                    <p class="text-gray-400 text-sm mb-2">
                        ⚠️ Jangan gunakan untuk tujuan ilegal
                    </p>
                    <p class="text-gray-400 text-sm">
                        💡 Built with PHP + MySQL + Tailwind
                    </p>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="border-t border-gray-800 mt-8 pt-6 text-center">
                <p class="text-gray-500 text-sm">
                    &copy; <?php echo date('Y'); ?> CTF Platform. Made with 💚 for learners.
                </p>
            </div>
        </div>
    </footer>

</body>
</html>