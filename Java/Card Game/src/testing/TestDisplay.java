/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package testing;

import games_set.card_games32.*;
import games_set.card_games32.sedma3.CHumanPlayer;
import java.awt.Canvas;
import java.awt.Graphics;
import java.awt.Image;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.awt.image.BufferedImage;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author mato
 */
public class TestDisplay extends Canvas{
    
    Image bufferedImage;
    Graphics backBuffer;
    boolean doubleBuffering=true;
    int width;
    int height;
    CPacket deck,trash;
    CHumanPlayer player;
    CPlayer[] players;
    
    public CGraphics graphics;
    public CGraphics computerGraphic;
    private int mouseX;
    private int mouseY;
    private MouseAdapter mouseListener=new MouseAdapter(){
        @Override
        public void mouseMoved(MouseEvent e){
            mouseX=e.getX();
            mouseY=e.getY();
            repaint();
        }
    };
    
    public TestDisplay()
    {
        super();
        graphics=null;
        this.addMouseMotionListener(mouseListener);
        deck=new CPacket(true);
        trash=new CPacket(false);
        players = new CPlayer[5];
        for(int i=0;i<players.length;i++){
            players[i]=new CPlayer("Computer "+(i+1),i+1);
        }
        try {
            trash.putCard(deck.takeCard());
        } catch (InvalidCardException ex) {
            Logger.getLogger(TestDisplay.class.getName()).log(Level.SEVERE, null, ex);
        }
        
        player=new CHumanPlayer("Estrom",0);
        
        for (int i=0;i<4;i++){
            player.draw(deck);
        }
        
        for (int i=0;i<players.length;i++){
            for (int j=0;j<4;j++){
                players[i].draw(deck);
            }
        }
    }
    
    
    
    @Override
    public void paint(Graphics d)
    {
        Graphics g;
        if(doubleBuffering)
        {
        
            if(width!=getWidth()||height!=getHeight()||
                    bufferedImage==null||backBuffer==null)
                resetBuffer();
            g=backBuffer;
        }
        else g=d;
        boolean active=false;
        CRendering.renderTable("Sedma berie 3",g, graphics, deck, trash, mouseX, mouseY,active,false);
        CRendering.renderPlayerHand(g, graphics, player, mouseX, mouseY,active);
        for (int i=0;i<players.length;i++){
            CRendering.renderOtherPlayerHand(g, computerGraphic, players[i], players.length);
        }
        CRendering.displayCardType(g, graphics, trash.getTopCard().getType());
        CRendering.renderSelectType(g, graphics, mouseX, mouseY);
        this.setCursor(CRendering.getCursor(graphics, player, mouseX, mouseY,active,false,true));
        if(doubleBuffering)
            d.drawImage(bufferedImage, 0, 0, null);
  
    }
    
    @Override
    public void update(Graphics g)
    {
        if(doubleBuffering)
            paint(g);
        else
            super.update(g);
    }

    private void resetBuffer() {
        width=getWidth();
        height=getHeight();
        
        if(bufferedImage!=null)
            bufferedImage.flush();
        if(backBuffer!=null)
            backBuffer.dispose();
        
        bufferedImage=new BufferedImage(width,height,BufferedImage.TYPE_INT_RGB);
        backBuffer=bufferedImage.getGraphics();
        
    }
}
