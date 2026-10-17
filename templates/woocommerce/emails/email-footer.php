<?php
/**
 * Email Footer (ModernRoad override): footer text below the card. Used once
 * the new design is installed.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 7.4.0
 */

defined('ABSPATH') || exit;
?>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!-- End Content -->
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- End Body -->
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <!-- Footer -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_footer">
                                        <tr>
                                            <td valign="top" id="credit">
                                                <?php
                                                echo wp_kses_post(
                                                    wpautop(
                                                        wptexturize(
                                                            /** This filter is documented in WooCommerce's templates/emails/email-footer.php */
                                                            apply_filters('woocommerce_email_footer_text', get_option('woocommerce_email_footer_text'))
                                                        )
                                                    )
                                                );
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Footer -->
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td><!-- Deliberately empty to support consistent sizing and layout across multiple email clients. --></td>
            </tr>
        </table>
    </body>
</html>
