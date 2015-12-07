/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.games_interface;

import games_set.CDisplay;
import java.awt.Graphics;
import java.awt.event.*;



/**
 *
 * @author martinm
 */

/*
 * This class consists of common interface which communicate with
 * selected game. Its main purpose is to manage games, switch, initialize, 
 * closes games and also handles user input interfaces and renders graphics
 * output.
 */
public final class CGameManager{
    
    static private CGame game=null;         //reference to game instance
    static private CDisplay Display=null;   //reference to display
    
    /* 
     * Mouse Adapter - receives mouse input from user and handles it
     * according to running game
     */
    static private MouseAdapter gameMouseEvents=new MouseAdapter(){
        @Override
        public void mouseClicked(MouseEvent e) {
            if(game==null||Display==null) return;
            game.mouseClicked(e);
            Display.repaint();
            }    
        @Override
        public void mousePressed(MouseEvent e) {
            if(game==null||Display==null) return;
            game.mousePressed(e);
            Display.repaint();
            
            }
        @Override
        public void mouseReleased(MouseEvent e) {
            if(game==null||Display==null) return;
            game.mouseReleased(e);
            Display.repaint();
        }
        @Override
        public void mouseEntered(MouseEvent e) {
            if(game==null||Display==null) return;
            game.mouseEntered(e);
            Display.repaint();
            
        }
        @Override
        public void mouseExited(MouseEvent e) {
            if(game==null||Display==null) return;
            game.mouseExited(e);
            Display.repaint();
        }
        @Override
        public void mouseDragged(MouseEvent e) {
            if(game==null||Display==null) return;
            game.mouseDragged(e);
            Display.repaint();
        }
        @Override
        public void mouseMoved(MouseEvent e) {
            if(game==null||Display==null) return;
            game.mouseMoved(e);
            Display.repaint();
        }
        @Override
        public void mouseWheelMoved(MouseWheelEvent e) {
            if(game==null||Display==null) return;
            game.mouseWheelMoved(e);
            Display.repaint();
        }
    };
    
    /*
     * KeyAdapter - receives key input from user and handles it
     * according to running game
     */
    static private KeyAdapter gameKeyEvents = new KeyAdapter(){
        @Override
        public void keyTyped(KeyEvent e) {
            if(game==null||Display==null) return;
            game.keyTyped(e);
            Display.repaint();
        }
        @Override
        public void keyPressed(KeyEvent e) {
            if(game==null||Display==null) return;
            game.keyPressed(e);
            Display.repaint();
        }
        @Override
        public void keyReleased(KeyEvent e) {
            if(game==null||Display==null) return;
            game.keyReleased(e);
            Display.repaint();
        }
    };
    
    private CGameManager(){
        
    }
    
    //sets up game main window
    public static void setDisplay(CDisplay d){
        Display=d;
        Display.addMouseListener(gameMouseEvents);
        Display.addMouseMotionListener(gameMouseEvents);
        Display.addMouseWheelListener(gameMouseEvents);
        Display.addKeyListener(gameKeyEvents);
        Display.enableDoubleBuffering(true);
    }
    
    //returns reference to game main window handle
    public static CDisplay getDisplay(){
        return Display;
    }
    
    //starts new game
    public static void setGame(CGame gm){
        if(game!=null)
            try
            {
                game.onClose();   //closes current game
            }catch(UnsupportedOperationException e)
            {
                
            }
        
    
        game=gm;
        game.onInit();
    }
    
    //resets game
    public static void resetGame(){
        if(game!=null)
        {
            try
            {
                game.restartGame();  //if restart is not implemented, closes game and starts again
            }catch(UnsupportedOperationException e){
                try
                {
                    game.onClose();
                }catch(UnsupportedOperationException e2)
                {
                
                }
                CSettings.startGame();
                
            }
        }
                  
            
    }
    
    //displays graphic output of running game
    public static void onRender(Graphics g){
        if(game!=null) 
            game.onRender(g);
    }
    
    //closes game
    public static void onClose(){
        if(game!=null) 
            try
            {
                game.onClose();
            }catch(UnsupportedOperationException e)
            {
                
            }
            
    }
    
    //exits current game
    public static void exitGame(){
        onClose();
        game=null;
    }
     
    //returns true if game is running
    public static boolean gameInitialized(){
        return game!=null;
    }       
}
