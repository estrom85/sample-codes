
package games_set.games_interface;

/**
 *
 * @author martinm
 */

import java.awt.Graphics;
import java.awt.event.*;

/*
 * this interface provides all methods neccesary for
 * programming the game module for this applet
 * this interface also includes also all possible human
 * interfaces such as mouse motion, mouse click, mouse wheel and
 * keyboard input.
 */

public abstract class CGame implements MouseListener,MouseMotionListener, 
        KeyListener, MouseWheelListener {
    /*
     * here comes all initializations of the game
     * if there are some settings then here should be
     * initialized all possible game settings
     */
    public abstract void onInit();
    /*
     * basic graphics interface. here comes code that 
     * will display graphic output of the game module
     * this method uses java.awt.Graphics class for
     * display graphical output
     */
    public abstract void onRender(Graphics g);
    /*
     * here comes all routines neccesary for closing the
     * module. This will free all alocated memory if necessary,
     * resets all settings that game uses if necessary.
     */
    public abstract void onClose();
    /*
     * here comes all routines needed to restart current game.
     */
    public abstract void restartGame();
    
    @Override
    public void mousePressed(MouseEvent e) {}

    @Override
    public void mouseReleased(MouseEvent e) {}

    @Override
    public void mouseEntered(MouseEvent e) {}

    @Override
    public void mouseExited(MouseEvent e) {}

    @Override
    public void mouseDragged(MouseEvent e) {}

    @Override
    public void mouseMoved(MouseEvent e) {}

    @Override
    public void keyTyped(KeyEvent e) {}

    @Override
    public void keyPressed(KeyEvent e) {}

    @Override
    public void keyReleased(KeyEvent e) {}

    @Override
    public void mouseWheelMoved(MouseWheelEvent e) {}
   


    
}
