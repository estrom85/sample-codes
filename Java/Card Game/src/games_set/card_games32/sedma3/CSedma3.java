/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.card_games32.sedma3;

import games_set.CDisplay;
import games_set.card_games32.*;
import games_set.games_interface.CGame;
import games_set.games_interface.CGameManager;
import games_set.games_interface.CSettings;
import java.awt.Color;
import java.awt.Font;
import java.awt.FontMetrics;
import java.awt.Graphics;
import java.awt.event.MouseEvent;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author mato
 */
public class CSedma3 extends CGame {
    //constants
    
    
    public enum Stage{
        MODE_SELECT,PLAY,TYPE_SELECT,VICTORY,ERROR
    }
    //display
    
    public CDisplay mDisplay;
    
    private CGraphics mDefaultGraphics;
    
    private CGraphics mOtherPlayerGraphics;
    //players
    private CPlayer[] mPlayers;
    
    private int mHumanPlayerIndex;
    
    private CPacket mDeck;
    
    private CPacket mTrash;
    
    //gameLogic
    private CGameLogic mGameLogic;
    
    //gameStage
    private Stage mGameStage;
    
    private boolean mGameActive;
    private boolean mSetTypeActive;
    
    private int mMouseX;
    private int mMouseY;
    
    private CMenu mMenu;
    
    @Override
    public void onInit() {
        
        //set graphics
        mDisplay=CGameManager.getDisplay();
        mDefaultGraphics=CGraphics.createGraphics(mDisplay.getWidth(), mDisplay.getHeight());
        //set mode
        setStage(Stage.MODE_SELECT);
        
        try {
            URL source=new URL(CSettings.getURL()+"/Images/karty.jpg");
            mDefaultGraphics.setFrontCardSet(source);
        } catch (MalformedURLException ex) {
            Logger.getLogger(CSedma3.class.getName()).log(Level.SEVERE, null, ex);
        }
        
        mOtherPlayerGraphics=CGraphics.createGraphics(mDisplay.getWidth(), mDisplay.getHeight(), 
                mDefaultGraphics.cardWidth()/2, mDefaultGraphics.cardHeight()/2);
        //set players
        int numOfPlayers=CSettings.getNumberOfPlayers();
        mPlayers=new CPlayer[numOfPlayers];
        mHumanPlayerIndex=-1;
        //init players
        for(int i=0;i<numOfPlayers;i++){
            if(!CSettings.getComputer(i)){
                mHumanPlayerIndex=i;
                mPlayers[i]=new CHumanPlayer(CSettings.getPlayerName(i),i);
            }
            else{
                mPlayers[i]=new CComputerPlayer(CSettings.getPlayerName(i),i);
            }
        }
        //init decks
        mDeck=new CPacket(true);
        mTrash=new CPacket(false);
        mGameLogic=null;
        
        //deal cards
        for(int i=0;i<numOfPlayers;i++){
            for(int j=0;j<4;j++)
                mPlayers[i].draw(mDeck);
        }
        try {
            //put top card into trash
            mTrash.putCard(mDeck.takeCard());
        } catch (InvalidCardException ex) {
            setStage(Stage.ERROR);
            Logger.getLogger(CSedma3.class.getName()).log(Level.SEVERE, null, ex);
        }
        
        mMouseX=0;
        mMouseY=0;
 
        mMenu=new CMenu(mDefaultGraphics);
        mMenu.addMenuItem(new CMenuItem("Normálna hra") {

            @Override
            public void execute() {
                mGameLogic=new CGameLogicNormal(getInstance(),mPlayers,mHumanPlayerIndex,mDeck,mTrash);
                setStage(Stage.PLAY);
            }
        });
        
        
        
    }
    private CSedma3 getInstance(){
        return this;
    }
    public void setStage(Stage stage){
        mGameStage=stage;
        switch(stage){
            case MODE_SELECT:
            case VICTORY:
            case ERROR:
                mGameActive=false;
                mSetTypeActive=false;
                break;
            case PLAY:
                mGameActive=true;
                mSetTypeActive=false;
                break;
            case TYPE_SELECT:
                mGameActive=false;
                mSetTypeActive=true;
                break;
                
        }
        
        mDisplay.repaint();
    }
    
    public void lockGame(){
        mGameActive=false;
    }
    
    public void unlockGame(){
        mGameActive=true;
    }
    
    @Override
    public void onRender(Graphics g) {
        CRendering.renderTable("Sedma berie 3", g, mDefaultGraphics, mDeck, mTrash,
                mMouseX,mMouseY,mGameActive,false);
        for(int i=0;i<mPlayers.length;i++){
            if(i==mHumanPlayerIndex)
                CRendering.renderPlayerHand(g, mDefaultGraphics, mPlayers[i], mMouseX, mMouseY, mGameActive);
            else
                CRendering.renderOtherPlayerHand(g, mOtherPlayerGraphics, mPlayers[i], mPlayers.length-1);
        }
        if(mGameLogic!=null){
            if(mTrash.getTopCard().getType()!=mGameLogic.getCurrentType())
                CRendering.displayCardType(g, mDefaultGraphics, mGameLogic.getCurrentType());
            mGameLogic.renderLogic(g, mDefaultGraphics);
        }
        
        if(mGameStage==Stage.TYPE_SELECT)
            CRendering.renderSelectType(g, mDefaultGraphics, mMouseX, mMouseY);
        
        if(mGameStage==Stage.MODE_SELECT)
            mMenu.renderMenu(g, mMouseX, mMouseY);
        if(mGameStage==Stage.VICTORY)
            renderVictory(g);
        
        
        mDisplay.setCursor(CRendering.getCursor(mDefaultGraphics, mPlayers[mHumanPlayerIndex], 
                mMouseX, mMouseY, mGameActive, false, mSetTypeActive));
    }

    private void renderVictory(Graphics g){
        CGraphics g_src=mDefaultGraphics;
        g.setColor(new Color(88,88,88,180));
        g.fillRect(0, 0, g_src.displayWidth(), g_src.displayHeight());
        
        
       
        String label="Výsledková listina:";
        g.setFont(new Font("Arial",Font.BOLD|Font.ITALIC,40));
        FontMetrics metrics=g.getFontMetrics();
        g.setColor(Color.RED);
        g.drawString(label, g_src.displayWidth()/2-metrics.stringWidth(label)/2,60);
        g.setFont(new Font("Arial",Font.BOLD|Font.ITALIC,20));
        metrics=g.getFontMetrics();
        for(int i=0;i<mPlayers.length;i++){
            int position=i+1;
            String player=position+". miesto: "+mPlayers[mGameLogic.getPosition(i)].name();
            g.drawString(player, g_src.displayWidth()/2-metrics.stringWidth(player)/2,i*40+120);
        }
        
    }
    @Override
    public void onClose() {
        mDisplay=null;
        mDefaultGraphics=null;
        mOtherPlayerGraphics=null;
    //players
        mPlayers=null;
        mHumanPlayerIndex=-1;
        mDeck=null;
        mTrash=null;
        mGameLogic=null;
        mGameStage=null;
        mGameActive=false;
        mSetTypeActive=false;
        mMouseX=0;
        mMouseY=0;
        mMenu=null;
        
    }

    @Override
    public void restartGame() {
        onClose();
        onInit();
    }

    @Override
    public void mouseClicked(MouseEvent e) {
        if(mGameStage==Stage.MODE_SELECT)
            mMenu.onClick(e.getX(), e.getY());
        if(mGameLogic!=null){
            if(mGameStage==Stage.PLAY)
                mGameLogic.selectCard(CRendering.getCardIndex(mPlayers[mHumanPlayerIndex], 
                        mDefaultGraphics, e.getX(), e.getY()));
            if(mGameStage==Stage.TYPE_SELECT)
                mGameLogic.selectType(CRendering.getType(mDefaultGraphics, e.getX(), e.getY()));
        }
    }
    
    @Override
    public void mouseMoved(MouseEvent e){
        mMouseX=e.getX();
        mMouseY=e.getY();
        mDisplay.repaint();
    }
    
}
