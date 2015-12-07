/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.card_games32.sedma3;

import games_set.card_games32.CCard;
import games_set.card_games32.CGraphics;
import games_set.card_games32.CPacket;
import games_set.card_games32.CPlayer;
import java.awt.Graphics;

/**
 *
 * @author mato
 */
abstract public class CGameLogic {
    protected int currentType;
    
    protected CSedma3 mGameInstance;
    protected CPlayer[] mPlayers;
    protected int mHumanPlayerIndex;
    protected CPacket mDeck,mTrash;
    protected int[] positions;
    
    public CGameLogic(CSedma3 gameInst, CPlayer[] players, int human_player, CPacket deck, CPacket trash){
        mGameInstance=gameInst;
        mPlayers=players;
        mHumanPlayerIndex=human_player;
        mDeck=deck;
        mTrash=trash;
        currentType=mTrash.getTopCard().getType();
    }
    
    public int getCurrentType(){
        return currentType;
    }
    
    abstract public void selectCard(int cardIndex);
    
    abstract public void selectType(int type);
    
    abstract public void renderLogic(Graphics g, CGraphics g_src);
    
    abstract public boolean isValid(CCard card);
    
    abstract public boolean isPlayable();
    
    public int getPosition(int i){
        if(i<0&&i>=positions.length)
            return -1;
        return positions[i];
    }
}
