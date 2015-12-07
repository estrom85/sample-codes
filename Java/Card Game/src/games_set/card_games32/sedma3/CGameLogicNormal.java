/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.card_games32.sedma3;

import games_set.card_games32.*;
import java.awt.Color;
import java.awt.Graphics;
import java.util.Timer;
import java.util.TimerTask;
import java.util.logging.Level;
import java.util.logging.Logger;
import sun.security.util.Debug;

/**
 *
 * @author mato
 */
public class CGameLogicNormal extends CGameLogic{
    
    private int mCurrentPlayer;
    private int mSevens;
    
    private int maxpos;
    private boolean endGame;
    private Timer mTimer= new Timer();
    
    public CGameLogicNormal(CSedma3 gameInst, CPlayer[] players, int human_player, CPacket deck, CPacket trash){
        super(gameInst,players,human_player,deck,trash);
        mCurrentPlayer=(int)(Math.random()*mPlayers.length);
        mSevens=0;
        positions=new int[mPlayers.length];
        for (int i=0;i<mPlayers.length;i++)
            positions[i]=-1;
        maxpos=0;
        endGame=false;
        
    }
    
    @Override
    public void selectCard(int cardIndex) {
       
        throwCard(cardIndex);
        //loop();
        //onLoop();
        setTimer();
    }

    @Override
    public void selectType(int type) {
        if(type<0||type>4)
            return;
        currentType=type;
        mGameInstance.setStage(CSedma3.Stage.PLAY);
        nextPlayer();
        //loop();
        //onLoop();
        setTimer();
    }

    @Override
    public void renderLogic(Graphics g, CGraphics g_src) {
        g.setColor(Color.red);
        g.drawString("Normálna hra",10,20);
        int draw=1;
        if(mSevens>0)
            draw=3*mSevens;
         String karta;
        switch(draw)
        {
            case 1:
                karta="kartu";
                break;
            case 2:
            case 3:
            case 4:
                karta="karty";
                break;
            default:
                karta="kariet";
                break;
        }
        g.drawString("Stiahni "+draw+" "+karta, 10, 40);
        g.drawString("V balíčku: "+mDeck.size(), 10, 60);
        g.drawString("V kope: "+mTrash.size(), 10, 80);
        for(int i=0;i<positions.length;i++){
            String text = null;
            if(positions[i]>-1){
                text=(i+1)+":"+mPlayers[positions[i]].name();
                g.drawString(text, 500, (i+1)*20);
            }
        }
    }
    
    /*
     * Main logic of game
     */
    //validate card
    @Override
    public boolean isValid(CCard card){
        //if sevens are active
        if(mSevens>0){
            if(card.getNumber()==0||(card.getNumber()==4&&card.getType()==1))
                return true;
            else
                return false;
        }
        if(card.getNumber()==5)
            return true;
        
        CCard topCard=mTrash.getTopCard();
        if(card.getType()==currentType||card.getNumber()==topCard.getNumber())
            return true;
        
        return false;
    }
    
    @Override
    public boolean isPlayable(){
        CPlayer player=mPlayers[mCurrentPlayer];
        for(int i=0;i<player.getNumberOfCards();i++){
            if(isValid(player.getCard(i)))
                return true;
        }
        return false;
    }
    
    private void activateTopCardEffect(){
        CCard topCard=mTrash.getTopCard();
        if(topCard.getNumber()==0)
            mSevens++;
        else if(topCard.getNumber()==7)
            checkAndMoveNext();
        else if(topCard.getNumber()==4&&topCard.getType()==1)
            mSevens=0;
        else if(topCard.getNumber()==5&&!mPlayers[mCurrentPlayer].isEmpty()){
            requestType();
            return;
        }
        checkAndMoveNext();
    
    }
    
    private void checkAndMoveNext(){
        setOrder();
        if(!endGame)
            nextPlayer();
    }
    
    private void setOrder(){
        if(mPlayers[mCurrentPlayer].isEmpty()){
            positions[maxpos]=mCurrentPlayer;
            maxpos++; 
        }
        /*
         * Checkes if all but one players finished its game
         * if only one player left, fills its position at the end of
         * scoreboard and notice the program, that the game is finished.
         */
        if (maxpos==mPlayers.length-1){
            mGameInstance.setStage(CSedma3.Stage.VICTORY);
            endGame=true;
            for(int i=0;i<mPlayers.length;i++){
                if(!mPlayers[i].isEmpty()){
                    positions[maxpos]=i;
                    break;
                }
            }
        }
    }
    
    
    private void nextPlayer() {
        if(endGame)
            return;
        mCurrentPlayer++;
        if(mCurrentPlayer==mPlayers.length)
            mCurrentPlayer=0;
        
        if(mPlayers[mCurrentPlayer].isEmpty())
            nextPlayer();
    }

    private void requestType() {
        if(mCurrentPlayer==mHumanPlayerIndex){
            mGameInstance.setStage(CSedma3.Stage.TYPE_SELECT);
            
        }
        else
            if(mPlayers[mCurrentPlayer] instanceof CComputerPlayer){
                currentType=((CComputerPlayer)mPlayers[mCurrentPlayer]).getType();
                nextPlayer();
            }
    }
    
    private void draw(){
        if(mSevens>0){
            for(int i=0;i<mSevens*3;i++){
                swapDecks();
                mPlayers[mCurrentPlayer].draw(mDeck);
            }
            mSevens=0;
        }
        else{
            swapDecks();
            mPlayers[mCurrentPlayer].draw(mDeck);
        }
        nextPlayer();
        
    }
    
    private void throwCard(int index){
        if(index==100){
            draw();
            return;
        }
        
        if(index<0||index>=mPlayers[mCurrentPlayer].getNumberOfCards())
            return;
        
        if(!isValid(mPlayers[mCurrentPlayer].getCard(index)))
            return;
        
        CCard thrown=mPlayers[mCurrentPlayer].throwCard(mTrash, index);
        this.currentType=thrown.getType();
        activateTopCardEffect();
        
        
    }
    
    private void swapDecks(){
        if(!mDeck.isEmpty())
            return;
        CCard topCard=mTrash.takeCard();
        //Debug.println("Deck1", ""+mDeck.size());
        //Debug.println("Trash1", ""+mTrash.size());
        while(mTrash.size()!=0)
            try {
                mDeck.putCard(mTrash.takeCard());
            } catch (InvalidCardException ex) {
                Debug.println("Error", ""+ex.getMessage());
                Logger.getLogger(CGameLogicNormal.class.getName()).log(Level.SEVERE, null, ex);
            }
        
        try {
            mTrash.putCard(topCard);
        } catch (InvalidCardException ex) {
            Logger.getLogger(CGameLogicNormal.class.getName()).log(Level.SEVERE, null, ex);
        }
        mDeck.shuffle();
        //Debug.println("Deck2", ""+mDeck.size());
        //Debug.println("Trash2", ""+mTrash.size());
        
    }
    
    private void setTimer(){
        mGameInstance.lockGame();
        mTimer.schedule(new TimerTask(){

                @Override
                public void run() {
                    onLoop();
                }
            }, 1000);
    }
    
    private void onLoop(){
        if(mCurrentPlayer!=mHumanPlayerIndex&&!endGame){
            
            if(mPlayers[mCurrentPlayer] instanceof CComputerPlayer){
                int cardIndex=((CComputerPlayer)mPlayers[mCurrentPlayer]).getCardIndex(this);
                throwCard(cardIndex);
                mGameInstance.mDisplay.repaint();
            }
            setTimer();
            
        }
        else{
            mGameInstance.unlockGame();
            //mTimer.cancel();
        }
        
    }
    
    
    private void loop(){
        
        
        
        
        mGameInstance.lockGame();
        while(mCurrentPlayer!=mHumanPlayerIndex&&!endGame){
            if(mPlayers[mCurrentPlayer] instanceof CComputerPlayer){
                int cardIndex=((CComputerPlayer)mPlayers[mCurrentPlayer]).getCardIndex(this);
                throwCard(cardIndex);
                mGameInstance.mDisplay.repaint();
                
                
                    /*
                    long start=new Date().getTime();
                    while(true){
                        long current=new Date().getTime();
                        if(start+1000<current)
                            break;
                    }
                    */
               
                
            }
        }
        mGameInstance.unlockGame();
    }
}
